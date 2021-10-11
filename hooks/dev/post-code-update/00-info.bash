#!/usr/bin/env bash

# Example: druphungary
site="${1}"

# Example: dev
target_env="${2}"

# Example: 2.x
#source_branch="${3}"

# Example: 2.x
#deployed_tag="${4}"

# Example: druphungary@svn-3224.prod.hosting.acquia.com:druphungary.git
#repo_url="${5}"

# Example: git
#repo_type="${6}"

cd "/var/www/html/${site}${target_env}" || exit 1
. './hooks/.includes/app.bash'

appLogger 'info' "BEGIN ${0}"
appEnvironmentInfo "${@}"
appLogger 'info' "END ${0}"
