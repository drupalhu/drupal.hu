#!/usr/bin/env bash

## Description: Generates ./.ddev/config.local.yaml file.
## Usage: generate:ddev-config-local-yaml
## Example: "ddev generate:ddev-config-local-yaml"

dst_filename='./.ddev/config.local.yaml'
platform='unknown'
case "${OSTYPE}" in
    solaris*) platform='solaris' ;;
    darwin*)  platform='darwin' ;;
    linux*)   platform='linux' ;;
    bsd*)     platform='bsd' ;;
    msys*)    platform='msys' ;;
esac

if [[ "${platform}" = 'linux' ]]; then
    ##
    # Force that the "nfs_mount_enabled" key is explicitly set, not just inherited.
    # Default value is "true", but that is not necessary on Linux systems.
    ##
    if [[ ! -f "${dst_filename}" ]]; then
        echo '{}' > "${dst_filename}"
    fi

    if [[ "$(yq eval '. | has("nfs_mount_enabled")' "${dst_filename}")" = 'false' ]]; then
        yq \
            eval \
            '.nfs_mount_enabled = false' \
            "${dst_filename}" \
            --inplace \
            --prettyPrint \
            --indent 2
    fi

    exit 0
fi
