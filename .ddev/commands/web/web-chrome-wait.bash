#!/usr/bin/env bash

## Description: Wait for the headless Chrome to be ready to accept connections.
## Usage: web:chrome:wait
## Example: "ddev web:chrome wait"

set -e

timeout_limit="${1:-10}"
[ -n "${timeout_limit}" ] && [ "${timeout_limit}" -eq "${timeout_limit}" ]


chrome_host_port="chrome:9222"
timeout_cmd=$(cat <<BASH
while ! curl -H 'host: localhost' --silent --show-error 'http://${chrome_host_port}/json/version';
do
    sleep 1;
    echo 'Waiting for Chromium on http://${chrome_host_port}';
done;

BASH
)

timeout \
    "${timeout_limit}" \
    bash \
    '-c' "${timeout_cmd}"
