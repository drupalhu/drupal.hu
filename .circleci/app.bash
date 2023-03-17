declare appDrushExecutable
declare appDrushSiteAlias
declare appDocroot

function appPhpExtensionInstall() {
    extDir="$(php -r 'echo ini_get("extension_dir");')"
    if [[ ! -f "${extDir}/yaml.so" ]]; then
        sudo apt-get -y install \
          libyaml-dev

        (yes '' || true) | sudo pecl install \
          pcov \
          yaml
    fi

    scanDir="$(php -i | grep --color=never --only-matching --perl-regexp '(?<=^Scan this dir for additional \.ini files => ).+')"

    if ! php -m | grep 'pcov' ; then
        echo 'extension=pcov' | sudo tee --append "${scanDir}/pcov.ini"
    fi

    if ! php -m | grep 'yaml' ; then
        echo 'extension=yaml' | sudo tee --append "${scanDir}/yaml.ini"
    fi
}

function appE2ePrepare() {
    appE2eInstallRequiredPackages
    appE2eNginx
    appE2ePhpFpm
    appE2eGoogleChrome
    appE2eSupervisor
}

function appE2eInstallRequiredPackages {
    sudo apt-get install -y \
        'nginx' \
        'sshpass' \
        'supervisor'
}

function appE2eNginx() {
    sudo find '/etc/nginx/sites-enabled'   '(' -type l -or -type f ')' -delete
    sudo find '/etc/nginx/sites-available' '(' -type l -or -type f ')' -delete

    sudo cp \
        --recursive \
        './.circleci/resources/etc/nginx/' \
        '/etc/'

    sudo sed \
        --in-place \
        --expression 's@user www-data;@user circleci;@g' \
        --expression 's@pid /run/nginx.pid;@pid /home/circleci/slash/var/run/nginx.pid;@g' \
        '/etc/nginx/nginx.conf'

    ( \
        cd /etc/nginx/sites-enabled/ \
        && \
        sudo ln -s '../sites-available/default.nginx' 'default.nginx' \
    )

    mkdir \
        --parents \
        "${HOME}/slash/var/run" \
        "${HOME}/slash/var/log/nginx"

    sudo /usr/sbin/nginx -t || return 1
    sudo service nginx start
}

function appE2ePhpFpm() {
    local etcDir='./.circleci/resources/etc/php-fpm'

    mkdir \
        --parents \
        "${HOME}/slash/var/run/php" \
        "${HOME}/slash/var/run/php-session" \
        "${HOME}/slash/var/log/php" \
        "${etcDir}/includes"

    while read -r envVarName; do
        if [[ "${!envVarName}" = '' ]]; then
            continue
        fi

        echo "env[${envVarName}]=${!envVarName}" >> "${etcDir}/includes/env.ini"
    done <<< "$( \
        grep \
            --color='never' \
            --only-matching \
            --no-filename \
            --perl-regexp \
            "(?<=getenv\(')(.+?)(?='\))" \
            ./docroot/sites/default/settings.php \
            ./docroot/sites/example.settings.local.php \
        | sort \
        | uniq \
    )"
}

function appE2eGoogleChrome() {
     mkdir \
        --parents \
        "${HOME}/slash/var/run/google-chrome.headless"
}

function appE2eSupervisor() {
    /usr/bin/supervisord --configuration "${HOME}/project/.circleci/resources/etc/supervisor/supervisord.ini"
}

function appAcquiaInstanceE2ePrepare() {
    src='.'
    dst='./artifacts/latest/acquia'

    mkdir --parents \
        "${dst}/docroot/sites/default/files" \
        "${dst}/sites/default/php_storage/twig" \
        "${dst}/sites/default/private" \
        "${dst}/sites/default/temporary"

    cp \
        "${src}/docroot/sites/default/settings.local.php" \
        "${dst}/docroot/sites/default/settings.local.php"

    cp \
        "${src}/sites/default/hash_salt.txt" \
        "${dst}/sites/default/hash_salt.txt"

    rsync \
        --recursive \
        --delete \
        "${src}/docroot/sites/default/files/" \
        "${dst}/docroot/sites/default/files/"

    rsync \
        --recursive \
        --delete \
        "${src}/sites/default/private/" \
        "${dst}/sites/default/private/"

    sudo sed \
        --in-place \
        --regexp-extended \
        --expression 's@set \$project_root   .+@set \$project_root   /home/$project_vendor/$project_name/artifacts/latest/acquia;@g' \
        '/etc/nginx/sites-available/default.nginx'
    sudo /usr/sbin/nginx -s reload
}

function appMysqlInstallRequiredPackages() {
    sudo apt-get install -y \
        mysql-client
}

function appMysqlPrepareCnf() {
    cat << INI >> ~/.my.cnf
[mysql]
prompt="\u@\h:\p/\dMySQL $ "
user=root
password=${MYSQL_ROOT_PASSWORD}
host=${MYSQL_HOST}
port=${MYSQL_PORT}
INI
}

function appMysqlWait() {
    local user='root'
    local pass="${MYSQL_ROOT_PASSWORD}"
    local host="${MYSQL_HOST}"
    local port="${MYSQL_PORT}"

    local timeout
    timeout='10'

    local callback
    callback="while ! mysqladmin ping --user='${user}' --password='${pass}' --host='${host}' --port='${port}';
do
    sleep 1;
done;"

    appWaitFor "${timeout}" "${callback}"
}

function appMysqlCreateMigrationSourceDatabase() {
    mysql \
      --execute="
        CREATE DATABASE ${MYSQL_DATABASE_LEGACY} /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;
        GRANT ALL PRIVILEGES ON ${MYSQL_DATABASE_LEGACY}.* TO '${MYSQL_USER}'@'%';
        FLUSH PRIVILEGES;
        SHOW CREATE DATABASE ${MYSQL_DATABASE_LEGACY};
      "
}

##
# @deprecated
##
function appNginxVirtualHost() {
    local projectRoot="${1:?projectRoot is required}"

    local workspace
    workspace="$(dirname "${projectRoot}")"

    local projectName
    projectName="$(basename "${projectRoot}")"

    # @todo The "phpName" variable name is inconsistent.
    # Actually it is "phpVersionId"
    local phpName
    phpName="$(appPhpbrewCurrent)"

    sed \
        --in-place \
        --expression="s@WORKSPACE@${workspace}@g" \
        --expression="s@PROJECT_NAME@${projectName}@g" \
        --expression="s@PUBLIC_HTML@${appDocroot}@g" \
        --expression="s@FASTCGI_PASS@unix:/var/run/php-fpm.${phpName}.prod.default.sock@g" \
        '/etc/nginx/servers/00.localhost.project.nginx'
}

function appLatestArtifactVersionNumber() {
    find ./artifacts \
        -mindepth 1 \
        -maxdepth 1 \
        -type d \
        -printf '%f\n' \
    | sort \
        --version-sort
}

function appWaitForHeadlessGoogleChrome() {
    local chromeHostPort
    chromeHostPort="${APP_CHROME_HOST}:${APP_CHROME_PORT}"

    local timeout
    timeout='10'

    local callback
    callback="while ! curl --silent --show-error 'http://${chromeHostPort}/json/version';
do
    sleep 1;
    echo 'Waiting for Google Chrome on http://${chromeHostPort}';
done;
"

    appWaitFor "${timeout}" "${callback}"
}

function appWaitForSupervisor() {
    local baseUrl="${1}"
    : "${baseUrl:?'baseUrl argument is required'}"

    local timeout="${2}"
    : "${timeout:?'timeout argument is required'}"

    local requestBody
    requestBody='<?xml version="1.0" encoding="utf-8"?>
<methodCall>
    <methodName>system.listMethods</methodName>
    <params/>
</methodCall>'

    local callback
    callback="while ! curl --silent --show-error --data '${requestBody}' '${baseUrl}/RPC2';
do
    sleep 1;
    echo 'Waiting for Supervisor on ${baseUrl}';
done;"

    appWaitFor "${timeout}" "${callback}"
}

function appWaitFor() {
    local timeout="${1}"
    : "${timeout:?'timeout argument is required'}"

    local callback="${2}"
    : "${callback:?'callback argument is required'}"

    timeout "${timeout}" bash -c -- "${callback}"
}

function appMarvinOnboarding() {
    local projectDir="${1:?Project directory is required}"
    local siteAlias="${2:?Drush site alias is required}"

    cd "${projectDir}" || exit 1

    local srcFileName="${projectDir}/.circleci/resources/project/drush/drush.host.yml"
    local dstFileName="${projectDir}/drush/drush.host.yml"
    cp "${srcFileName}" "${dstFileName}"

    ${appDrushExecutable} \
        "${appDrushSiteAlias}" \
        -vv \
        app:onboarding \
        --uri='http://localhost'

    local siteDir
    siteDir="$(${appDrushExecutable} "${siteAlias}" 'core:status' --field='site' --format='string')"
    siteDir="${siteDir#sites/}"

    ${appDrushExecutable} \
        "${appDrushSiteAlias}" \
        -vv \
        app:runtime-environment:switch
}

##
# @deprecated
#
# @see drush app:test:config-status
##
function appConfigStatusCheck() {
    local drupalRoot="${1:?Drupal root directory is required}"
    local siteAlias="${2:?Drush site alias is required}"

    configStatus="$(${appDrushExecutable} --root="${drupalRoot}" "${appDrushSiteAlias}" config:status --format='json')"
    if [[ "${configStatus}" == '[]' ]]; then
        return 0
    fi

    # @todo Get config_sync_directory.
    for configId in $(echo "${configStatus}" | jq -r '.[] | .name' -)
    do
        echo "--- ${configId}"
        diff \
            --color='always' \
            <(${appDrushExecutable} --root="${drupalRoot}" "${appDrushSiteAlias}" config:get "${configId}") \
            "./sites/default/config/prod/${configId}.yml"
    done

    return 1
}

function appSshKnownHostsAdd() {
    local host="${1}"
    : "${host:?'host argument is required'}"

    [ -d ~/.ssh ] || mkdir ~/.ssh
    touch ~/.ssh/known_hosts
    # Check if it is already added.
    if ssh-keygen -F "${host}"; then
        # Remove the existing entry.
        ssh-keygen -R "${host}"
    fi

    # Add a fresh one.
    ssh-keyscan -H "${host}" >> ~/.ssh/known_hosts
}

# region Debug
function appDebugConfigFiles() {
    dumper=('batcat' '--force-colorization')

    "$(composer config bin-dir)/drush" \
      --config='drush' \
      @app.local \
      app:drush:config \
    | "${dumper[@]}" --language='yaml' --file-name='drush app:drush:config'

    "${dumper[@]}" ./drush/drush.local.yml || true
    "${dumper[@]}" ./docroot/sites/default/settings.local.php || true
    "${dumper[@]}" ./sites/default/hash_salt.txt || true
    "${dumper[@]}" ./behat.local.yml || true
    "${dumper[@]}" ./phpcs.xml || true
    "${dumper[@]}" ./phpstan.neon || true
    "${dumper[@]}" ./phpunit.xml || true

    return 0
}

function appDebugSshConfig() {
    ls -la ~/.ssh || true
    cat ~/.ssh/known_hosts || true
    cat ~/.ssh/config || true
}

function appDebugAcquiaGitAccess() {
    local ahProjectId
    ahProjectId="$(yq eval '.marvin.acquia.projectId' './.circleci/resources/project/drush/drush.host.yml')"

    local ahGitHost
    ahGitHost="$(yq eval '.marvin.acquia.gitHost' './.circleci/resources/project/drush/drush.host.yml')"

    git ls-remote "${ahProjectId}@${ahGitHost}:${ahProjectId}.git"
}

function appDebugAcquiaEnvAccess() {
    local ahEnv="${1}"
    : "${ahEnv:?'ahEnv argument is required'}"

    ahUser="$(yq eval ".${ahEnv}.user" './drush/sites/app.site.yml')" || return 1
    ahHost="$(yq eval ".${ahEnv}.host" './drush/sites/app.site.yml')" || return 2
    ssh "${ahUser}@${ahHost}" 'pwd ; ls -la' || return 3

    return 0
}
# endregion
