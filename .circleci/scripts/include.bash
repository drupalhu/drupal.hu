declare appDrushExecutable
declare appDrushSiteAlias
declare appDocroot
declare appMysqlHost

function appPhpCliIniFileName() {
    local phpExecutable="${1:-php}"

    "${phpExecutable}" -i |
        grep \
            --color=never \
            --only-matching \
            --perl-regexp '(?<=Loaded Configuration File => ).+'
}

function appIsPhpZendExtension() {
    local extensionName="${1:?Extension name is required}"

    [[ "${extensionName}" == 'xdebug' ]]
}

function appPhpCliPresetActivate() {
    local preset="${1}"
    : "${preset:?required}"

    local etcDir
    etcDir="$(phpbrew path etc)"

    local configScanDir
    configScanDir="$(phpbrew path config-scan)"

    rm --force "${etcDir}/php.ini"
    if [[ ! -f "${etcDir}/php.${preset}.ini" ]]; then
        echo 1>&2 "ERROR: file '${etcDir}/php.${preset}.ini' doesn't exists."

        return 1
    fi

    ln --symbolic "php.${preset}.ini" "${etcDir}/php.ini" || return $?

    if [[ "${preset}" = 'xdebug2' ]]; then
        if [[ ! -f "${configScanDir}/xdebug2.ini" && -f "${configScanDir}/xdebug2.ini.tmp" ]]; then
            mv --force "${configScanDir}/xdebug2.ini.tmp" "${configScanDir}/xdebug2.ini"
        fi

        if [[ -f "${configScanDir}/xdebug3.ini" ]]; then
            mv --force "${configScanDir}/xdebug3.ini" "${configScanDir}/xdebug3.ini.tmp"
        fi
    fi

    if [[ "${preset}" = 'xdebug3' ]]; then
        if [[ ! -f "${configScanDir}/xdebug3.ini" && -f "${configScanDir}/xdebug3.ini.tmp" ]]; then
            mv --force "${configScanDir}/xdebug3.ini.tmp" "${configScanDir}/xdebug3.ini"
        fi

        if [[ -f "${configScanDir}/xdebug2.ini" ]]; then
            mv --force "${configScanDir}/xdebug2.ini" "${configScanDir}/xdebug2.ini.tmp"
        fi
    fi

    php -r 'opcache_reset();'

    return 0
}

function appPhpCliExtensionEnable() {
    local extensionName="${1:?Extension name is required}"
    local phpExecutable="${2:-php}"

    local iniFileName
    iniFileName="$(appPhpCliIniFileName "${phpExecutable}")"

    local extensionType
    extensionType='extension'
    if appIsPhpZendExtension "${extensionName}"; then
        extensionType='zend_extension'
    fi

    sed \
        --in-place \
        --regexp-extended \
        --expression "s/^${extensionType}(.+)\/${extensionName}(\.so){0,1}$/;\0/g" \
        "${iniFileName}"

    ${phpExecutable} -m | grep --silent --ignore-case "${extensionName}"

    return $?
}

function appPhpCliExtensionDisable() {
    local extensionName="${1:?Extension name is required}"
    local phpExecutable="${2:-php}"

    local iniFileName
    iniFileName="$(appPhpCliIniFileName "${phpExecutable}")"

    local extensionType='extension'
    if appIsPhpZendExtension "${extensionName}"; then
        extensionType='zend_extension'
    fi

    sed \
        --in-place \
        --regexp-extended \
        --expression "s/^(;|#)( |\t){0,}(${extensionType}(.+)\/${extensionName}(\.so){0,1})$/\3/g" \
        "${iniFileName}"

    ${phpExecutable} -m | ( ! grep --silent "${extensionName}" )

    return $?
}

function appMysqlPrepareCnf() {
    cat << INI >> ~/.my.cnf
[mysql]
prompt="\u@\h:\p/\dMySQL $ "
user=root
password=${MYSQL_ROOT_PASSWORD}
host=${appMysqlHost}
port=${MYSQL_PORT}
INI
}

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

function appWaitForMysql() {
    local user="${1}"
    local pass="${2}"
    local host="${3}"
    local port="${4}"

    timeout \
        20 \
        bash -c \
        -- \
        "while ! mysqladmin ping --user=\"${user}\" --password=\"${pass}\" --host=\"${host}\" --port=\"${port}\";
        do
            sleep 1;
        done;"
}

function appWaitForHeadlessChromium() {
    local chromiumHostPort="${1:?Host and port of Chromium is required}"

    timeout \
        10 \
        bash -c \
        -- \
        "while ! curl --silent --show-error http://${chromiumHostPort}/json/version;
        do
            sleep 1;
            echo Waiting for Chromium on http://${chromiumHostPort};
        done;"
}

function appMarvinOnboarding() {
    local projectDir="${1:?Project directory is required}"
    local siteAlias="${2:?Drush site alias is required}"

    cd "${projectDir}" || exit 1
    ${appDrushExecutable} "${appDrushSiteAlias}" \
        marvin:onboarding \
        --url='http://localhost'

    local siteDir
    siteDir="$(${appDrushExecutable} "${siteAlias}" 'core:status' --field='site' --format='string')"

    appMarvinOnboardingSettingsLocalPhp "${projectDir}" "${siteDir}"
}

function appCheckConfigStatus() {
    local drupalRoot="${1:?Drupal root directory is required}"
    local siteAlias="${2:?Drush site alias is required}"

    configStatus="$(${appDrushExecutable} --root="${drupalRoot}" "${appDrushSiteAlias}" config:status --format=json)"
    if [[ "${configStatus}" == '[]' ]]; then
        return 0
    fi

    echo "${configStatus}"
    ${appDrushExecutable} --root="${drupalRoot}" "${appDrushSiteAlias}" config:export --yes
    git diff

    return 1
}

function appPhpbrewCurrent() {
    phpbrew list \
    | grep \
        --color=never \
        --only-matching \
        --perl-regexp '(?<=^\* )[^\s+]+'
}

function appMarvinOnboardingSettingsLocalPhp() {
    local projectDir="${1:?Project directory is required}"
    local siteDir="${2:?Site directory is required}"

    local settingsPhpFileName="${projectDir}/${appDocroot}/${siteDir}/settings.local.php"

    if [[ -d "$(dirname "${settingsPhpFileName}")" ]]; then
        mkdir --parents "$(dirname "${settingsPhpFileName}")"
    fi

    if [[ ! -f "${settingsPhpFileName}" ]]; then
        cat << PHP > "${fileName}"
<?php

/**
 * @file
 * Local settings.
 */

PHP
    fi

    cat << PHP >> "${settingsPhpFileName}"
\$databases['default']['default']['username'] = '${MYSQL_USER}';
\$databases['default']['default']['password'] = '${MYSQL_PASSWORD}';
\$databases['default']['default']['host'] = '${appMysqlHost}';
\$databases['default']['default']['port'] = '${MYSQL_PORT}';
\$databases['default']['default']['database'] = '${MYSQL_DATABASE}';

\$databases['migrate']['default'] = \$databases['default']['default'];
\$databases['migrate']['default']['database'] = 'migrate';

\$settings['trusted_host_patterns'] = ['^localhost\$'];

PHP


    fileNames=("$(find "./${siteDir}/config/prod" -name 'search_api.server.*.yml')")
    for fileName in ${fileNames[*]}
    do
        if [[ "$(yq eval --no-colors '.backend_config.connector' "${fileName}")" != 'solr_acquia_connector' ]]; then
            continue
        fi

        name="$(yq eval --no-colors '.id' "${fileName}")"
        cat << PHP >> "${settingsPhpFileName}"
\$config['search_api.server.${name}']['backend'] = 'search_api_solr';
\$config['search_api.server.${name}']['backend_config']['connector'] = 'standard';
\$config['search_api.server.${name}']['backend_config']['connector_config'] = [
  'scheme' => 'http',
  'host' => 'solr',
  'port' => 8983,
  'path' => '/',
  'core' => '${name}',
  'timeout' => 5,
  'index_timeout' => 10,
  'optimize_timeout' => 15,
  'finalize_timeout' => 30,
  'commit_within' => 1000,
  'solr_version' => '7',
  'http_method' => 'AUTO',
  'jmx' => FALSE,
  'solr_install_dir' => '',
];

PHP
    done
}
