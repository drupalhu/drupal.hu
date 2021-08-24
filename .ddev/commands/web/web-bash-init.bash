#!/usr/bin/env bash

## Description: Fine tuning of the Bash shell.
## Usage: web:bash:init
## Example: "ddev web:bash:init"

set -e

echo "alias d='drush --config=drush @app.local'" >> ~/.bash_aliases

# @todo Read the project root (/var/www/html) from configuration.
sudo sed \
    --in-place \
    --expression 's/\/var\/www\/html\/vendor\/bin/\/var\/www\/html\/bin/g' \
    '/etc/bashrc/commandline-addons.bashrc'
