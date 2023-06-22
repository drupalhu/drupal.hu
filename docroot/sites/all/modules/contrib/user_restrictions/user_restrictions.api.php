<?php

/**
 * @file
 * Hooks provided by the User restrictions module.
 */

/**
 * @addtogroup hooks
 * @{
 */

/**
 * Allows other modules to change user restrictions.
 *
 * @param $denied
 *   The value of the restriction; TRUE when the restriction is enabled.
 * @param $error
 *   The message to return to the user when the restriction is enabled.
 * @param $context
 *   An array containing more information about the restriction being checked.
 */
function hook_user_restrictions_alter(&$denied, &$error, &$context) {
}

/**
 * Allows modules to give information about the implemented restrictions.
 *
 * @param $info
 *   An array containing information about the restrictions implemented by
 *   modules.
 * @param $context
 *   A string containing the ID of the required information.
 */
function hook_user_restrictions_info(&$info, &$context) {
}

/**
 * Allows to alter the restriction information returned by other modules.
 *
 * @param $info
 *   An array containing information about the restrictions implemented by
 *   modules.
 * @param $context
 *   A string containing the ID of the required information.
 */
function hook_user_restrictions_info_alter(&$info, &$context) {
}

/**
 * @} End of "addtogroup hooks".
 */

