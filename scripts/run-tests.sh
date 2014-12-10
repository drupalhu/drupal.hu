#!/bin/bash
set -ev

if [ "${TRAVIS_PULL_REQUEST}" == "true" ]; then
  drush test-run Webform --uri=http://127.0.0.1:8080
fi

if [ "${TRAVIS_PULL_REQUEST}" == "false" ]; then
  drush test-run Webform --uri=http://127.0.0.1:8080
fi
