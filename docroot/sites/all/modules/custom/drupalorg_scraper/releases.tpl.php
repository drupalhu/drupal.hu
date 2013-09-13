<?php

/**
 * @file
 * Formats current Drupal version numbers for active branches.
 *
 * It provides links to the Hungarian translation packages too.
 *
 * @ingroup themeable
 */
?>

<div class="current">
  <a href="<?php print $releases[7]['download_link'] ?>"
     title="<?php print t('Download Drupal @ver', array('@ver' => $releases[7]['version'])); ?>">Drupal <?php print $releases[7]['version'] ?></a>
  <a class="translation-download" href="<?php print $releases[7]['translation'] ?>"
    title="<?php print t('Hungarian language package for @ver.x version', array('@ver' => 7)); ?>"><?php print t('Translation'); ?></a>
</div>
<div class="previous">
  <a href="<?php print $releases[6]['download_link'] ?>"
    title="<?php print t('Download Drupal @ver', array('@ver' => $releases[6]['version'])); ?>">Drupal <?php print $releases[6]['version'] ?></a>
  <a class="translation-download" href="<?php print $releases[6]['translation'] ?>"
    title="<?php print t('Hungarian language package for @ver.x version', array('@ver' => 6)); ?>"><?php print t('Translation'); ?></a>
</div>