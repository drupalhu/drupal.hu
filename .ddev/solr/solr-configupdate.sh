#!/usr/bin/env bash

##
# How to use this script:
# - ddev ssh --service solr
# - /docker-entrypoint-initdb.d/solr-configupdate.sh
# - exit
# - ddev stop
# - ddev start
##

set -e

src_cores_dir='/mnt/ddev_config/solr/cores'
dst_cores_dir='/var/solr/data'

core_names="$(find "${src_cores_dir}" -mindepth 1 -maxdepth 1 -printf '%f ')"
for core_name in ${core_names};
do
    rm \
        --force \
        --recursive \
        "${dst_cores_dir}/${core_name}/conf"

    mkdir -p "${dst_cores_dir}/${core_name}/conf"
    cp \
        --recursive \
        "${src_cores_dir}/${core_name}/conf/" \
        "${dst_cores_dir}/${core_name}/"
done
