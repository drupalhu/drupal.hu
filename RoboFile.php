<?php

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Robo\Collection\CollectionBuilder;
use Robo\Contract\TaskInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class RoboFile extends \Robo\Tasks implements LoggerAwareInterface {

  use LoggerAwareTrait;

  /**
   * @var string
   */
  protected $drupalRoot = 'docroot';

  /**
   * @var string
   */
  protected $sitesSubDir = 'default';

  /**
   * @var \Symfony\Component\Filesystem\Filesystem
   */
  protected $fs;

  public function __construct() {
    $this->fs = new Filesystem();
  }

  /**
   * @command site:install
   */
  public function siteInstall(): TaskInterface {
    return $this->getTaskSiteInstall();
  }

  /**
   * @command site:reinstall
   */
  public function siteReinstall(): CollectionBuilder {
    return $this
      ->collectionBuilder()
      ->addTask($this->getTaskCleanFiles())
      ->addTask($this->getTaskCleanPrivate())
      ->addTask($this->getTaskSiteInstall());
  }

  /**
   * @command clean:files
   */
  public function cleanFiles() {
    return $this->getTaskCleanFiles();
  }

  /**
   * @command clean:private
   */
  public function cleanPrivate() {
    return $this->getTaskCleanPrivate();
  }

  /**
   * @command composer:post-install-cmd
   * @hidden
   */
  public function composerPostInstallCmd(): CollectionBuilder {
    return $this
      ->collectionBuilder()
      ->addCode($this->getTaskGenerateHashSalt())
      ->addTask($this->getTaskCreateDirectories())
      ->addCode($this->getTaskCreateSettingsLocalPhp());
  }

  /**
   * @command composer:post-update-cmd
   * @hidden
   */
  public function composerPostUpdateCmd(): CollectionBuilder {
    return $this
      ->collectionBuilder()
      ->addCode($this->getTaskGenerateHashSalt())
      ->addTask($this->getTaskCreateDirectories())
      ->addCode($this->getTaskCreateSettingsLocalPhp());
  }

  protected function getTaskCleanFiles(): TaskInterface {
    return $this->getTaskFileSystemRemoveDirectDescendants("{$this->drupalRoot}/sites/{$this->sitesSubDir}/files");
  }

  protected function getTaskCleanPrivate(): TaskInterface {
    return $this->getTaskFileSystemRemoveDirectDescendants("sites/{$this->sitesSubDir}/private");
  }

  protected function getTaskSiteInstall(): TaskInterface {
    return $this
      ->taskExecStack()
      ->exec("bin/drush --no-interaction site:install 'dhup'")
      ->exec("bin/drush --no-interaction config:set system.site uuid 'ef1a6582-4250-4739-b2e5-1316d872fc0b'")
      ->exec('bin/drush --no-interaction config:import');
  }

  protected function getTaskFileSystemRemoveDirectDescendants(string $dir): TaskInterface {
    return $this
      ->taskFilesystemStack()
      ->remove((new Finder())->in($dir)->depth('== 0'));
  }

  protected function getTaskCreateDirectories(): TaskInterface {
    return $this
      ->taskFilesystemStack()
      ->mkdir([
        "sites/{$this->sitesSubDir}/private",
        "sites/{$this->sitesSubDir}/temporary",
        "sites/{$this->sitesSubDir}/translations",
      ]);
  }

  protected function getTaskCreateSettingsLocalPhp(): \Closure {
    return function (): int {
      $src = "{$this->drupalRoot}/sites/example.settings.local.php";
      if (!$this->fs->exists($src)) {
        $this->logger->warning(sprintf(
          'Source file does not exists: "%s"',
          $src
        ));

        return 0;
      }

      $dst = "{$this->drupalRoot}/sites/{$this->sitesSubDir}/settings.local.php";
      if ($this->fs->exists($dst)) {
        $this->logger->warning(sprintf(
          'Destination file already exists: "%s"',
          $dst
        ));

        return 0;
      }

      $this->fs->copy($src, $dst);

      return 0;
    };
  }

  protected function getTaskGenerateHashSalt(): \Closure {
    return function (): int {
      $fileName = "sites/{$this->sitesSubDir}/hash_salt.txt";
      if ($this->fs->exists($fileName)) {
        $this->logger->notice(sprintf('File "%s" already exists.', $fileName));

        return 0;
      }

      $this->fs->dumpFile($fileName, $this->generateHashSalt());

      return 0;
    };
  }

  protected function generateHashSalt(): string {
    return uniqid(mt_rand(), true);
  }

}
