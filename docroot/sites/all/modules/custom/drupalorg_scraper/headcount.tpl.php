<?php
/**
 * @file
 * Default theme implementation to display a drupal.org statistics block.
 *
 * Available variables:
 * - $people: number of people using Drupal
 * - $countries: number of countries using Drupal
 * - $languages: number of languages using Drupal
 */
 ?>
<span><em><?php print number_format($people, 0, ",", " ") ?></em> személy <em><?php print $countries ?></em> országból a Drupalt választotta.</span>
