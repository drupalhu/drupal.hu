#!/usr/bin/env bash

## Description: Adds human friendly packages and configurations to the "chrome" container in order to make the debugging easier.
## Usage: chrome:humanize
## Example: "ddev chrome:humanize"

set -e

project_name="$(ddev describe --json-output | jq -r '.raw.name')"
service_name='chrome';
container_name="ddev-${project_name}-${service_name}"

packages=(
    'curl'
    'mc'
    'tree'
    'iputils-ping'
)

# There is a `ddev exec --service chrome` command,
# but the "user" is not configurable, so it uses the default user,
# which is "chrome", and it has no enough permission.
# That is why the regular `docker exec` is used.
docker exec --user 'root' "${container_name}" apt-get -y update
docker exec --user 'root' "${container_name}" apt-get -y install "${packages[@]}"
