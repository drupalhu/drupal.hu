#!/usr/bin/env bash

## Description: Initialize MySQL settings.
## Usage: web:mysql:init
## Example: "ddev web:mysql:init"

set -e

sql_query=$(cat <<SQL
CREATE DATABASE IF NOT EXISTS \`${MYSQL_DATABASE_LEGACY}\`;
GRANT ALL PRIVILEGES ON ${MYSQL_DATABASE_LEGACY}.* TO '${MYSQL_USER}'@'%';
FLUSH PRIVILEGES;

SQL
)

mysql \
    --user='root' \
    --password="${MYSQL_ROOT_PASSWORD}" \
    --host="${MYSQL_HOST}" \
    --port="${MYSQL_PORT}" \
    --execute="${sql_query}"
