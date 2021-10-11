#!/usr/bin/env bash

site="${1}"
target_env="${2}"
#db_name="${3}"
#source_env="${4}"



cd "/var/www/html/${site}${target_env}" || exit 1
. './hooks/.includes/app.bash'

appLogger 'info' "BEGIN ${0}"
appEnvironmentInfo "${@}"
appLogger 'info' "END ${0}"
