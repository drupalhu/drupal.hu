
# Drupal.hu

@todo


## Requirements

* `nvm --version` >= 0.34
* `yarn --version` >= 1.12 
  * `npm --global update yarn`


## Contribute

1. Run `git clone --branch=8.x1.x https://github.com/drupalhu/drupal.hu.git drupal.hu`
1. Run `cd drupal.hu`
1. Run `nvm install`
1. Run `nvm use`
1. Run `composer install`
1. Run `alias d='bin/drush --config=drush @app.local'`
1. Run `d marvin:onboarding`
1. Run `$EDITOR docroot/sites/default/settings.local.php`
   * Database connection
1. Run `$EDITOR drush/drush.local.yml` 
   * `commands.options.uri`
1. Run `$EDITOR tests/behat/behat.local.yml` 
   * `default.extensions.Behat\MinkExtension.base_url`
1. Run `d marvin:build`
1. Run `d site:install appp --existing-config`
1. Run `d marvin:lint`
1. Run `d marvin:test:unit`
1. Run `d marvin:test:behat`
