declare appDrushExecutable
declare appDrushSiteAlias
declare appDocroot

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

function appPhpCliExtensionEnable() {
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
        --expression "s/^${extensionType}(.+)\/${extensionName}(\.so){0,1}$/;\0/g" \
        "${iniFileName}"
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
}

function appNginxVirtualHost() {
    local projectRoot="${1:?projectRoot is required}"

    local workspace
    workspace="$(dirname "${projectRoot}")"

    local projectName
    projectName="$(basename "${projectRoot}")"

    local phpName
    phpName="$(php -r 'echo PHP_VERSION_ID;')"

    sed \
        --in-place \
        --expression="s@WORKSPACE@${workspace}@g" \
        --expression="s@PROJECT_NAME@${projectName}@g" \
        --expression="s@PUBLIC_HTML@${appDocroot}@g" \
        --expression="s/PHP_NAME/${phpName}/g" \
        --expression='s/PHP_VARIANT/prod/g' \
        '/etc/nginx/vhosts.d/00.localhost.project.nginx'
}

function appLatestArtifactVersionNumber() {
    find ./artifacts \
        -mindepth 1 \
        -maxdepth 1 \
        -type d \
        -printf '%f\n' |
        sort \
            --version-sort
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
    projectDir="${1:?Project directory is required}"
    siteAlias="${2:?Drush site alias is required}"

    cd "${projectDir}" || exit 1
    $appDrushExecutable "${appDrushSiteAlias}" \
        marvin:onboarding \
        --url='http://localhost'

    local siteDir
    siteDir="$(${appDrushExecutable} "${siteAlias}" 'core:status' --field='site' --format='string')"

    appMarvinOnboardingSettingsLocalPhp "${projectDir}" "${siteDir}"
}

function appMarvinOnboardingSettingsLocalPhp() {
    projectDir="${1:?Project directory is required}"
    siteDir="${2:?Site directory is required}"

    fileName="${projectDir}/${appDocroot}/${siteDir}/settings.local.php"

    if [[ -d "$(dirname "${fileName}")" ]]; then
        mkdir --parents "$(dirname "${fileName}")"
    fi

    if [[ ! -f "${fileName}" ]]; then
        cat << PHP > "${fileName}"
<?php

/**
 * @file
 * Local settings.
 */

PHP
    fi

    cat << PHP >> "${fileName}"
\$databases['default']['default']['username'] = '${MYSQL_USER}';
\$databases['default']['default']['password'] = '${MYSQL_PASSWORD}';
\$databases['default']['default']['host'] = '${MYSQL_HOST}';
\$databases['default']['default']['port'] = '${MYSQL_PORT}';
\$databases['default']['default']['database'] = '${MYSQL_DATABASE}';

\$config['search_api.server.general']['backend'] = 'search_api_solr';
\$config['search_api.server.general']['backend_config']['connector'] = 'standard';
\$config['search_api.server.general']['backend_config']['connector_config'] = [
  'scheme' => 'http',
  'host' => '${SOLR_HOST}',
  'port' => ${SOLR_PORT},
  'path' => '/',
  'core' => '${SOLR_CORE}',
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

\$settings['trusted_host_patterns'] = ['^localhost\$'];
PHP
}
