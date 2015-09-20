<?php

// Configuration for patch handler commands.
// https://bitbucket.org/davereid/drush-patchfile
$command_specific['patch-add']
  = $command_specific['patch-apply-all']
  = $command_specific['patch-project']
  = $command_specific['patch-status']
  = array(
    'patch-file' => '../patches.make',
  );
