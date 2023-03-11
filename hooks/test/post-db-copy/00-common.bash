#!/usr/bin/env bash

site="${1}"
: "${site:?required}"

targetEnv="${2}"
: "${targetEnv:?required}"

#dbName="${3}"
#: "${dbName:?required}"

#sourceEnv="${4}"
#: "${sourceEnv:?required}"

projectRoot="$([ "${PWD:t}" = 'livedev' ] && echo "${PWD}" || echo "/var/www/html/${site}${targetEnv}")"

cd "${projectRoot}" || exit 1
. './hooks/.includes/app.zsh'

onPostDbCopy "${@}"
