<?php

declare(strict_types = 1);

namespace DrupalHu\DrupalHu\Tests\FunctionalJavascript;

use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Url;
use Sweetchuck\DrupalTestTraits\Core\FinderTrait;
use Sweetchuck\DrupalTestTraits\Core\System\MessageTrait;
use Sweetchuck\DrupalTestTraits\EntityLegal\EntityLegalTrait;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;
use Webmozart\PathUtil\Path;
use weitzman\DrupalTestTraits\ExistingSiteWebDriverTestBase;
use weitzman\DrupalTestTraits\ScreenShotTrait;

class TestBase extends ExistingSiteWebDriverTestBase {

  use ScreenShotTrait;
  use FinderTrait;
  use MessageTrait;
  use EntityLegalTrait;

  protected array $finderSettings = [];

  /**
   * User role config entity identifiers.
   *
   * @var string[]
   */
  protected array $userRoles = [];

  public function getContainer() {
    return $this->container;
  }

  protected function setUp() {
    parent::setUp();
    $this->initFinders();
  }

  protected function getProjectRoot(): string {
    return Path::join(__DIR__, '..', '..', '..');
  }

  /**
   * Same as the $sites_path variable in settings.php.
   */
  protected function getSitesPath(): string {
    return 'sites/default';
  }

  protected function getConfigSyncDirectory(): string {
    return Path::join($this->getProjectRoot(), $this->getSitesPath(), 'config', 'prod');
  }

  protected function initFinders() {
    $this->initFindersDrupalCoreSystemMessage();
  }

  protected function getFinderSettings(): array {
    if (!$this->finderSettings) {
      $fileName = Path::join($this->getProjectRoot(), 'tests', 'behat', 'config', 'extension.drupal.yml');
      $config = Yaml::parseFile($fileName);
      $this->finderSettings = $config['default']['extensions']['NuvoleWeb\Drupal\DrupalExtension']['selectors'] ?? [];
    }

    return $this->finderSettings;
  }

  /**
   * Button label "Log in" vs multilingual site :-(.
   */
  protected function drupalLogin(AccountInterface $account) {
    if ($this->loggedInUser) {
      $this->drupalLogout();
    }

    $this->drupalGet(Url::fromRoute('user.login'));
    $this->submitForm(
      [
        'name' => $account->getAccountName(),
        'pass' => $account->passRaw,
      ],
      'edit-submit',
    );

    // @see ::drupalUserIsLoggedIn()
    $account->sessionId = $this->getSession()->getCookie(\Drupal::service('session_configuration')->getOptions(\Drupal::request())['name']);
    $this->assertTrue(
      $this->drupalUserIsLoggedIn($account),
      (string) (new FormattableMarkup(
        'User %name successfully logged in.',
        ['%name' => $account->getAccountName()],
      )),
    );

    $this->loggedInUser = $account;
    $this->container->get('current_user')->setAccount($account);
  }

  protected function getUserRoles(): array {
    if (!$this->userRoles) {
      $files = (new Finder())
        ->in($this->getConfigSyncDirectory())
        ->name('/^user\.role\.[^\.]+\.yml$/');
      foreach ($files as $file) {
        $parts = explode('.', $file->getBasename());
        $this->userRoles[$parts[2]] = $parts[2];
      }
    }

    return $this->userRoles;
  }

}
