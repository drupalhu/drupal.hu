#!/usr/bin/env bash

function appLogger() {
	local level="${1}"
	local message="${2}"

	if [[ "${APP_LOG_LEVEL:-error.warning.info}" =~ ${level} ]]; then
		echo 1>&2 "APP ${level} - ${message}"
	fi
}

function appEnvironmentInfo() {
	appLogger 'debug' "\$SHELL = ${SHELL}"
	appLogger 'debug' "${SHELL} --version = $(${SHELL} --version)"
	appLogger 'debug' "\$PWD = ${PWD}"
	appLogger 'debug' "git --version = $(git --version)"
	appLogger 'debug' "\$* = $*"
	appLogger 'debug' "$(env | sort)"
}
