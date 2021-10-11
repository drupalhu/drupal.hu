#!/usr/bin/env bash

site="${1}"
target_env="${2}"
#source_branch="${3}"
#deployed_tag="${4}"
#repo_url="${5}"
#repo_type="${6}"

cd "/var/www/html/${site}${target_env}" || exit 1
. './hooks/.includes/app.bash'

appLogger 'info' "BEGIN ${0}"
appEnvironmentInfo "${@}"
appLogger 'info' "END ${0}"
