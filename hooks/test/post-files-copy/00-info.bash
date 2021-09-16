#!/usr/bin/env bash

projectRoot=$(dirname "$(dirname "$(dirname "$(dirname "${BASH_SOURCE[0]}")")")")
# shellcheck source=./hooks/.includes/app.bash
. "${projectRoot}/hooks/.includes/app.bash"

appLogger 'info' "BEGIN ${0}"
appEnvironmentInfo "${@}"


appLogger 'info' "END ${0}"
