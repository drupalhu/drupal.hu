#!/usr/bin/env zsh

site="${1}"
: "${site:?required}"

targetEnv="${2}"
: "${site:?required}"

#sourceEnv="${3}"
#: "${sourceEnv:?required}"

projectRoot="$([ "${PWD:t}" = 'livedev' ] && echo "${PWD}" || echo "/var/www/html/${site}${targetEnv}")"

cd "${projectRoot}" || exit 1
. './hooks/.includes/app.zsh'

onPostFilesCopy "${@}"
