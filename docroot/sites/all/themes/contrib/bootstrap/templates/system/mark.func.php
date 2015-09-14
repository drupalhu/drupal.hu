<?php
/**
 * @file
 * mark.func.php
 */

/**
 * Overrides theme_mark().
 */
function bootstrap_mark($variables) {
  global $user;
  if ($user->uid) {
    if ($variables['type'] == MARK_NEW) {
      return ' <span class="marker label label-primary">' . t('new') . '</span>';
    }
    elseif ($variables['type'] == MARK_UPDATED) {
      return ' <span class="marker label label-info">' . t('updated') . '</span>';
    }
  }
}
