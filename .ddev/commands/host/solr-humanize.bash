#!/usr/bin/env bash

## Description: Adds human friendly packages and configurations to the "solr" container in order to make the debugging easier.
## Usage: solr:humanize
## Example: "ddev solr:humanize"

set -e

# @todo Maybe this one is better:
# project_name="$(ddev describe --json-output | jq -r '.raw.name')"
project_name="$(yq eval '.name' './.ddev/config.yaml')"
if [[ -s './.ddev/config.local.yaml' ]]; then
    project_name_local="$(yq eval '.name' './.ddev/config.local.yaml')"
    if [[ "${project_name_local}" != '' && "${project_name_local}" != 'null' ]]; then
        project_name="${project_name_local}"
    fi
fi

service_name='solr';
container_name="ddev-${project_name}-${service_name}"

packages=(
    'mc'
    'tree'
)

# There is a `ddev exec --service solr` command,
# but the "user" is not configurable, so it uses the default user,
# which is "solr", and it has no enough permission.
# That is why the regular `docker exec` is used.
docker exec --user 'root' "${container_name}" apt-get -y update
docker exec --user 'root' "${container_name}" apt-get -y install "${packages[@]}"
docker exec --user 'root' "${container_name}" mkdir -p '/home/solr'
docker exec --user 'root' "${container_name}" chown 'solr:solr' --recursive '/home/solr'
