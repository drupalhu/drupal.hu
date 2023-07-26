#!/usr/bin/env bash

#region hooks
function onPostCodeDeploy() {
    #local site="${1}"
    #local targetEnv="${2}"
    #local sourceBranch="${3}"
    #local deployedTag="${4}"
    #local repoUrl="${5}"
    #local repoType="${6}"

    appLogger 'BEGIN onPostCodeDeploy'
    appEnvironmentInfo "${@}"

    local returnCode=0

    if [[ "${AH_NON_PRODUCTION}" = '1' ]]; then
        appLocalBackupRestore "$(appLocalBackupRestoreDir)" || returnCode=$?
    fi

    appUpdate || returnCode=$?

    if [[ "${AH_NON_PRODUCTION}" = '1' ]]; then
        appStagingConfig || returnCode=$?
    fi

    appLogger 'END onPostCodeDeploy'

    return "${returnCode}"
}

function onPostCodeUpdate() {
    #site="${1}"
    #targetEnv="${2}"
    #sourceBranch="${3}"
    #deployedTag="${4}"
    #repoUrl="${5}"
    #repoType="${6}"

    appLogger 'BEGIN onPostCodeUpdate'
    appEnvironmentInfo "${@}"

    local returnCode=0

    if [[ "${AH_NON_PRODUCTION}" = '1' ]]; then
        appLocalBackupRestore "$(appLocalBackupRestoreDir)" || returnCode=$?
    fi

    appUpdate || returnCode=$?

    if [[ "${AH_NON_PRODUCTION}" = '1' ]]; then
        appStagingConfig || returnCode=$?
    fi

    appLogger 'END onPostCodeUpdate'

    return "${returnCode}"
}

function onPostDbCopy() {
    #site="${1}"
    #targetEnv="${2}"
    #dbName="${3}"
    #sourceEnv="${4}"

    appLogger 'BEGIN onPostDbCopy'
    returnCode=0

    appUpdate || returnCode=$?
    appLogger 'END onPostDbCopy'

    return "${returnCode}"
}

function onPostFilesCopy() {
    #site="${1}"
    #targetEnv="${2}"
    #sourceEnv="${3}"

    returnCode=0

    appLogger 'BEGIN onPostFilesCopy'
    echo 'Nothing to do.'
    appLogger 'END onPostFilesCopy'

    return "${returnCode}"
}
#endregion

#region appLocalBackupRestore
function appLocalBackupRestore() {
    local backupDir="${1}"
    : "${backupDir:?'backupDir argument is required'}"

    local returnCode=0

    while read -r site
    do
        appLocalBackupRestoreSite "${backupDir}" "${site}" || returnCode=$?
    done <<< "$(appSites)"

    return "${returnCode}"
}

function appLocalBackupRestoreSite() {
    local backupDir="${1}"
    : "${backupDir:?'backupDir argument is required'}"

    local site="${2}"
    : "${site:?'site name is required'}"

    appLocalBackupRestoreSiteDatabases "${backupDir}" "${site}"
    appLocalBackupRestoreSiteFiles "${backupDir}" "${site}"
}

function appLocalBackupRestoreSiteDatabases() {
    local backupDir="${1}"
    : "${backupDir:?'backupDir argument is required'}"

    local site="${2}"
    : "${site:?'site name is required'}"

    local returnCode=0

    while read -r database
    do
        appLocalBackupRestoreSiteDatabase "${backupDir}" "${site}" "${database}" || returnCode=$?
    done <<< "$(appDatabases "${backupDir}/${site}/database")"

    return "${returnCode}"
}

function appLocalBackupRestoreSiteDatabase() {
    local backupDir="${1}"
    : "${backupDir:?'site specific backup directory is required'}"

    local site="${2}"
    : "${site:?'site name is required'}"

    local database="${3}"
    : "${database:?'database argument is required'}"

    local fileName="${backupDir}/${site}/database/${database}.mysql"

    appLogger 'info' "BEGIN database import site:${site} database:${database} file:${fileName}"
    ./vendor/bin/drush --config='drush' site:set "${PWD}/docroot#${site}"

    ./vendor/bin/drush \
        --config='drush' \
        --yes \
        --database="${database}" \
        sql:drop || return 1

    ./vendor/bin/drush \
        --config='drush' \
        --yes \
        --database="${database}" \
        sql:cli \
        < "${fileName}" || return 2

    ./vendor/bin/drush --config='drush' site:set
    appLogger 'info' "END database import"
}

function appLocalBackupRestoreSiteFiles() {
    local backupDir="${1}"
    : "${backupDir:?'site specific backup directory is required'}"

    local site="${2}"
    : "${site:?'site name is required'}"

    local returnCode=0

    while read -r dir
    do
        appLocalBackupRestoreSiteFile "${backupDir}" "${site}" "${dir}" || returnCode=$?
    done <<< "$(find "${backupDir}/${site}/file" -mindepth 1 -maxdepth 1 -type d -printf '%P\n')"

    return "${returnCode}"
}

function appLocalBackupRestoreSiteFile() {
    local backupDir="${1}"
    : "${backupDir:?'site specific backup directory is required'}"

    local site="${2}"
    : "${site:?'site name is required'}"

    local dir="${3}"
    : "${dir:?'dir argument is required'}"

    # @todo This only works if the Drupal instance is fully functional.
    srcDir="${backupDir}/${site}/file/${dir}"
    dstDir="${PWD}/docroot/$(./vendor/bin/drush --config='drush' status --format='list' --fields="${dir}")"
    appLogger 'info' "BEGIN file sync src:${srcDir} dst:${dstDir}"
    ./vendor/bin/drush --config='drush' site:set "${PWD}/docroot#${site}"

    ./vendor/bin/drush \
        --config='drush' \
        --yes \
        core:rsync \
        "${srcDir}"\
        "${dstDir}"\
        -- \
        --delete || return 1

    ./vendor/bin/drush --config='drush' site:set
    appLogger 'info' "END file sync"
}

function appLocalBackupRestoreDir() {
    echo "${APP_LOCAL_BACKUP_DIR:-${HOME}/backup/prod}"
}
#endregion

#region appUpdate
function appUpdate() {
    local returnCode=0

    while read -r site
    do
        appUpdateSite "${site}" || returnCode=$?
    done <<< "$(appSites)"

    return "${returnCode}"
}

function appUpdateSite() {
    local site="${1}"
    : "${site:?'site argument is required'}"

    ./vendor/bin/drush site:set "${PWD}/docroot#${site}"

    ./vendor/bin/drush --config='drush' --yes updatedb --no-post-updates || return 1
    ./vendor/bin/drush --config='drush' --yes config:import || return 2
    ./vendor/bin/drush --config='drush' --yes updatedb || return 3

    ./vendor/bin/drush --config='drush' cache:rebuild

    nonEnglishLangCodes="$(appNonEnglishLangCodes)"
    if [[ "${nonEnglishLangCodes}" != '' ]]; then
        ./vendor/bin/drush --config='drush' --yes locale:check  || return 4
        ./vendor/bin/drush --config='drush' --yes locale:update || return 5
    fi

    # @todo SearchAPI reindex if it is necessary.
    ./vendor/bin/drush --config='drush' cache:rebuild

    ./vendor/bin/drush site:set
}
#endregion

function appStagingConfig() {
    appHttpAuthEnable || returnCode=$?
    appMailSafetyEnable || returnCode=$?
}

function appHttpAuthEnable() {
    local drush=('./vendor/bin/drush' '--config="drush"' '--yes')

    "${drush[@]}" pm:enable 'shield' \
    && \
    "${drush[@]}" config:set 'shield.settings' 'credentials.shield.user' "${APP_HTTP_AUTH_USER}" \
    && \
    "${drush[@]}" config:set 'shield.settings' 'credentials.shield.pass' "${APP_HTTP_AUTH_PASS}" \
    && \
    "${drush[@]}" config:set 'shield.settings' 'shield_enable' 'true'
}

function appMailSafetyEnable() {
    local drush=('./vendor/bin/drush' '--config="drush"' '--yes')

    "${drush[@]}" pm:enable 'mail_safety' \
    && \
    "${drush[@]}" config:set 'mail_safety.settings' 'enabled' 'true' \
    && \
    "${drush[@]}" config:set 'mail_safety.settings' 'send_mail_to_dashboard' 'true' \
    && \
    "${drush[@]}" config:set 'mail_safety.settings' 'send_mail_to_default_mail' 'false'
}

#region Helper functions
function appLogger() {
    local level="${1}"
    local message="${2}"
    local dateTime
    dateTime="$(date '+%F-%H-%M-%S')"

    if [[ "${APP_LOG_LEVEL:-error.warning.info}" =~ ${level} ]]; then
        echo 1>&2 "${dateTime} ${level} APP ${message}"
    fi
}

function appEnvironmentInfo() {
    appLogger 'debug' "\${SHELL} = ${SHELL}"
    appLogger 'debug' "\${SHELL} --version = $(${SHELL} --version)"
    appLogger 'debug' "\${PWD} = ${PWD}"
    appLogger 'debug' "git --version = $(git --version)"
    appLogger 'debug' "\${*} = ${*}"
    appLogger 'debug' "$(env | sort)"
}

function appSites() {
    find \
        './docroot/sites' \
        -mindepth '2' \
        -maxdepth '2' \
        \( \
            -type f \
            -or \
            -type l \
        \) \
        -name 'settings.php' \
        -printf '%h\n' \
    | \
    sed --expression 's@./docroot/sites/@@g'
}

function appDatabases() {
    parentDir="${1}"
    : "${parentDir:?'is required'}"

    find \
        "${parentDir}" \
        -mindepth '1' \
        -maxdepth '1' \
        '(' \
            -type f \
            -or \
            -type l \
        ')' \
        -name '*.mysql' \
        -printf '%P\n' \
    | sed --expression 's@\.mysql@@g'
}

function appNonEnglishLangCodes() {
    script=$(cat <<'PHP'
$entityTypeId = 'configurable_language';
$etm = \Drupal::entityTypeManager();

if (!$etm->hasDefinition($entityTypeId)) {
    return 0;
}

$languages = $etm
    ->getStorage($entityTypeId)
    ->loadMultiple(NULL);
unset(
    $languages[\Drupal\Core\Language\LanguageInterface::LANGCODE_NOT_APPLICABLE],
    $languages[\Drupal\Core\Language\LanguageInterface::LANGCODE_NOT_SPECIFIED],
    $languages['en'],
);

fwrite(STDOUT, implode(PHP_EOL, array_keys($languages)) . PHP_EOL);

    return 0;
PHP
)

    ./vendor/bin/drush --config='drush' php:eval "${script}"
}
#endregion
