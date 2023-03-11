#!/usr/bin/env bash

## Description: Generates ".ddev/config.local.yaml" and ".ddev/.env" files to override the default port numbers.
## Usage: config:port-offset
## Example: "ddev config:port-offset 2000"

base="${1}"
: "${base:?'port number base argument is required and it has to be an integer'}"

if ! [[ "${base}" =~ ^[1-9][0-9]*$ ]];
then
    echo 1>&2 "port number base is not a positive integer: ${base}"

    exit 2;
fi

offset=0

yq_expressions=()
yq_expressions+=(".router_http_port      = $(( base + offset ))"); (( offset+=1 ));
yq_expressions+=(".router_https_port     = $(( base + offset ))"); (( offset+=1 ));
yq_expressions+=(".mailhog_port          = $(( base + offset ))"); (( offset+=1 ));
yq_expressions+=(".mailhog_https_port    = $(( base + offset ))"); (( offset+=1 ));
yq_expressions+=(".phpmyadmin_port       = $(( base + offset ))"); (( offset+=1 ));
yq_expressions+=(".phpmyadmin_https_port = $(( base + offset ))"); (( offset+=1 ));

ddev_config_file_name='./.ddev/config.local.yaml'
if [[ ! -f "${ddev_config_file_name}" ]]; then
    echo '{}' > "${ddev_config_file_name}"
fi

for yq_expression in "${yq_expressions[@]}";
do
    yq \
        eval \
        "${yq_expression}" \
        "${ddev_config_file_name}" \
        --inplace \
        --prettyPrint \
        --indent 2
done


env_vars=(
    'APP_CHROME_EXPOSE_HEADLESS_PORT'

    'APP_SOLR_EXPOSE_HTTP_PORT'
    'APP_SOLR_EXPOSE_HTTPS_PORT'
)

env_file_name='./.ddev/.env'
if [[ ! -f "${env_file_name}" ]]; then
    cp "${env_file_name}.example" "${env_file_name}"
fi

if [[ ! -s "${env_file_name}" ]]; then
    echo '# Override docker-compose environment variables.' >> "${env_file_name}"
fi

for env_var in "${env_vars[@]}"; do
    port_number=$(( base + offset ))

    if grep "${env_var}" "${env_file_name}" 1>/dev/null; then
        sed \
            --in-place \
            --regexp-extended \
            --expression "s/^(#){0,1}${env_var}(=.*|\n)/${env_var}=${port_number}/g" \
            "${env_file_name}"
    else
        echo "${env_var}=${port_number}" >> "${env_file_name}"
    fi

    (( offset+=1 ))
done
