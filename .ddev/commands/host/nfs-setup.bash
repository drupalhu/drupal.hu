#!/usr/bin/env bash

## Description: NFS service checker/runner based on OS.
## Usage: nfs:setup
## Example: "ddev nfs:setup"

nfs_mount_enabled="$(yq eval '.nfs_mount_enabled' './.ddev/config.yaml')"
if [[ -f './.ddev/config.local.yaml' ]]; then
    result="$(yq eval '.nfs_mount_enabled' './.ddev/config.local.yaml')"
    if [[ "${result}" = 'true' || "${result}" = 'false' ]]; then
        nfs_mount_enabled="${result}"
    fi
fi

if [[ "${nfs_mount_enabled}" != 'true' ]]; then
    echo 1>&2 ''
    echo 1>&2 'NFS mount is disabled.'
    echo 1>&2 ''

    exit 0;
fi

if pgrep -x 'nfsd' &> /dev/null; then
    echo 1>&2 ''
    echo 1>&2 'NFS service is already running.'
    echo 1>&2 ''

    ddev debug nfsmount

    exit $?;
fi

platform='unknown'
case "${OSTYPE}" in
    solaris*) platform='solaris' ;;
    darwin*)  platform='darwin' ;;
    linux*)   platform='linux' ;;
    bsd*)     platform='bsd' ;;
    msys*)    platform='msys' ;;
esac

if [[ ! -f "${SCRIPTS_PATH}/nfs-setup-${platform}.bash" ]]; then
    echo 1>&2 ''
    echo 1>&2 "NFS service could not be started because of the unsupported OS type: ${OSTYPE}"
    echo 1>&2 ''

    exit 2
fi

sh "${SCRIPTS_PATH}/nfs-setup-${platform}.bash" || exit 3
ddev debug nfsmount
