#!/usr/bin/env bash

site="${1}"
: "${site:?required}"

targetEnv="${2}"
: "${targetEnv:?required}"

#sourceBranch="${3}"
#: "${sourceBranch:?required}"

#deployedTag="${4}"
#: "${deployedTag:?required}"

#repoUrl="${5}"
#: "${repoUrl:?required}"

#repoType="${6}"
#: "${repoType:?required}"

projectRoot="$([ "${PWD:t}" = 'livedev' ] && echo "${PWD}" || echo "/var/www/html/${site}${targetEnv}")"

cd "${projectRoot}" || exit 1
. './hooks/.includes/app.bash'

onPostCodeDeploy "${@}"
