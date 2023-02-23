#!/usr/bin/env bash

## Description: Fine tuning of the Bash shell.
## Usage: web:bash:init
## Example: "ddev web:bash:init"

set -e

if [[ ! -f ~/.bash_aliases ]]; then
    touch ~/.bash_aliases
fi

if ! grep 'alias d=' ~/.bash_aliases ; then
    echo "alias d='drush --config=drush @app.local'" >> ~/.bash_aliases
fi
