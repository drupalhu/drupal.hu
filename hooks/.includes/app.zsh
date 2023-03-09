#!/usr/bin/env zsh

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

    setopt XTRACE
    if [[ "${AH_NON_PRODUCTION}" = '1' ]]; then
        appLocalBackupRestore "$(appLocalBackupRestoreDir)" || returnCode=$?
    fi

    appUpdate || returnCode=$?

    if [[ "${AH_NON_PRODUCTION}" = '1' ]]; then
        appHttpAuthEnable "${APP_HTTP_AUTH_USER}" "${APP_HTTP_AUTH_PASS}" || returnCode=$?
        appMailSafetyEnable || returnCode=$?
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

    setopt XTRACE
    if [[ "${AH_NON_PRODUCTION}" = '1' ]]; then
        appLocalBackupRestore "$(appLocalBackupRestoreDir)" || returnCode=$?
    fi

    appUpdate || returnCode=$?

    if [[ "${AH_NON_PRODUCTION}" = '1' ]]; then
        appHttpAuthEnable "${APP_HTTP_AUTH_USER}" "${APP_HTTP_AUTH_PASS}" || returnCode=$?
        appMailSafetyEnable || returnCode=$?
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

    setopt XTRACE
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
    setopt XTRACE
    echo 'Nothing to do.'
    unsetopt XTRACE
    appLogger 'END onPostFilesCopy'

    return "${returnCode}"
}
#endregion

#region appLocalBackupRestore
function appLocalBackupRestore() {
    local backupDir="${1}"
    : "${backupDir:?'backupDir argument is required'}"

    local returnCode=0

    setopt XTRACE

    local sites=("${(@f)$(find "${backupDir}" -mindepth 1 -maxdepth 1 -type d)}")
    for site in "${sites[@]}"; do
        appLocalBackupRestoreSite "${site}" || returnCode=$?
    done

    return "${returnCode}"
}

function appLocalBackupRestoreSite() {
    local backupDir="${1}"
    : "${backupDir:?'site specific backup directory is required'}"

    setopt XTRACE
    appLocalBackupRestoreSiteDatabases "${backupDir}"
    appLocalBackupRestoreSiteFiles "${backupDir}"
}

function appLocalBackupRestoreSiteDatabases() {
    local backupDir="${1}"
    : "${backupDir:?'site specific backup directory is required'}"

    local returnCode=0

    setopt XTRACE
    local fileNames=("${(@f)$(find "${backupDir}/database" -mindepth 1 -maxdepth 1 -type f -name '*.mysql')}")
    for fileName in "${fileNames[@]}"; do
        appLocalBackupRestoreSiteDatabase "${backupDir}" "${fileName:t:r}" || returnCode=$?
    done

    return "${returnCode}"
}

function appLocalBackupRestoreSiteDatabase() {
    local backupDir="${1}"
    : "${backupDir:?'site specific backup directory is required'}"

    local database="${2:-default}"
    : "${database:?'database argument is required'}"

    local site="${backupDir:t}"

    local fileName="${backupDir}/database/${database}.mysql"

    appLogger 'info' "BEGIN database import site:${site} database:${database} file:${fileName}"
    ./vendor/bin/drush --config='drush' site:set "${PWD}/docroot#${site}"
    setopt XTRACE

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

    unsetopt XTRACE

    ./vendor/bin/drush --config='drush' site:set
    appLogger 'info' "END   database import"
}

function appLocalBackupRestoreSiteFiles() {
    local backupDir="${1}"
    : "${backupDir:?'site specific backup directory is required'}"

    local returnCode=0

    setopt XTRACE

    local dirs=("${(@f)$(find "${backupDir}/file" -mindepth 1 -maxdepth 1 -type d)}")
    for dir in "${dirs[@]}"; do
        appLocalBackupRestoreSiteFile "${backupDir}" "${dir:t}" || returnCode=$?
    done

    return "${returnCode}"
}

function appLocalBackupRestoreSiteFile() {
    local backupDir="${1}"
    : "${backupDir:?'site specific backup directory is required'}"

    local dir="${2:-files}"
    : "${dir:?'dir argument is required'}"

    local site="${backupDir:t}"

    srcDir="${backupDir}/file/${dir}"

    appLogger 'info' "BEGIN file sync site:${site} dir:${dir} srcDir:${srcDir}"
    ./vendor/bin/drush --config='drush' site:set "${PWD}/docroot#${site}"
    setopt XTRACE
    # @todo This only works if the Drupal instance is fully functional.
    dstDir="${PWD}/docroot/$(./vendor/bin/drush --config='drush' status --format='list' --fields="${dir}")"

    ./vendor/bin/drush \
        --config='drush' \
        --yes \
        core:rsync \
        "${srcDir}"\
        "${dstDir}"\
        -- \
        --delete || return 1
    unsetopt XTRACE

    ./vendor/bin/drush --config='drush' site:set
    appLogger 'info' "END   file sync"
}

function appLocalBackupRestoreDir() {
    echo "${APP_LOCAL_BACKUP_DIR:-${HOME}/backup/prod}"
}
#endregion

#region appUpdate
function appUpdate() {
    local sites=("${(@f)$(appSites)}")
    local returnCode=0

    setopt XTRACE
    for site in "${sites[@]}"; do
        appUpdateSite "${site}" || returnCode=$?
    done

    return "${returnCode}"
}

function appUpdateSite() {
    local site="${1}"
    : "${site:?'site argument is required'}"

    ./vendor/bin/drush site:set "${PWD}/docroot#${site}"

    setopt XTRACE
    ./vendor/bin/drush --config='drush' --yes updatedb      || return 1
    ./vendor/bin/drush --config='drush' --yes config:import || return 2

    nonEnglishLangCodes="$(appNonEnglishLangCodes)"
    if [[ "${nonEnglishLangCodes}" != '' ]]; then
        ./vendor/bin/drush --config='drush' --yes locale:check  || return 3
        ./vendor/bin/drush --config='drush' --yes locale:update || return 4
    fi
    unsetopt XTRACE

    ./vendor/bin/drush site:set
}
#endregion

function appHttpAuthEnable() {
    local user="${1}"
    : "${user:?'user argument is required'}"

    local pass="${2}"
    : "${pass:?'pass argument is required'}"

    ./vendor/bin/drush \
        --config='drush' \
        --yes \
        pm:enable \
        'shield' \
    && \
    ./vendor/bin/drush \
        --config='drush' \
        --yes \
        config:set \
        'shield.settings' \
        'credentials.shield.user' \
        "${user}" \
    && \
    ./vendor/bin/drush \
        --config='drush' \
        --yes \
        config:set \
        'shield.settings' \
        'credentials.shield.pass' \
        "${pass}"

    appLogger 'info' 'shield module is activated'
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
