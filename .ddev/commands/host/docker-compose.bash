#!/usr/bin/env bash

## Description: Wrapper for docker-compose.
## Usage: docker-compose
## Example: "ddev docker-compose images"

docker-compose \
    --file './.ddev/.ddev-docker-compose-full.yaml' \
    --project-name "ddev-$(ddev describe --json-output | jq -r '.raw.name')" \
    "${@}"
