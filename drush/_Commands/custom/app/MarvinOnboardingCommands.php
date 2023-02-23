<?php

declare(strict_types = 1);

namespace Drush\Commands\app;

use Consolidation\AnnotatedCommand\Output\OutputAwareInterface;
use Drupal\marvin\ComposerInfo;
use Drush\Commands\marvin\CommandsBase;
use League\Container\ContainerAwareInterface;
use League\Container\ContainerAwareTrait;
use Robo\Common\OutputAwareTrait;
use Robo\Result;
use Symfony\Component\Filesystem\Filesystem;
use Webmozart\PathUtil\Path;

class MarvinOnboardingCommands extends CommandsBase implements
    ContainerAwareInterface,
    OutputAwareInterface {

  use ContainerAwareTrait;
  use OutputAwareTrait;

  protected Filesystem $fs;

  public function __construct(?ComposerInfo $composerInfo = NULL, ?Filesystem $fs = NULL) {
    parent::__construct($composerInfo);
    $this->fs = $fs ?: new Filesystem();
  }

  /**
   * @hook post-command marvin:onboarding
   *
   * @link https://github.com/Sweetchuck/drupal-marvin_product/issues/26 Issue #26 - marvin:onboarding vs phpunit.xml
   * @link https://github.com/Sweetchuck/drupal-marvin_product/issues/47 Issue #47 - marvin:onboarding delegate
   */
  public function cmdMarvinOnboardingPostExecute(Result $result) {
    $this
      ->collectionBuilder()
      ->addCode($this->getTaskMarvinOnboardingPhpunit())
      ->run();
  }

  public function getTaskMarvinOnboardingPhpunit(): \Closure {
    return function (): int {
      $input = $this->input();
      $logger = $this->getLogger();
      $dst = './phpunit.xml';

      if ($this->fs->exists($dst)) {
        $logger->debug(
          'File "<info>{fileName}</info>" already exists',
          ['fileName' => $dst],
        );

        return 0;
      }

      $this->fs->copy('./phpunit.xml.dist', $dst);

      $doc = new \DOMDocument();
      $doc->loadXML(file_get_contents($dst));

      $projectRootDirAbs = Path::makeAbsolute($this->getProjectRootDir(), getcwd());

      $url = $input->hasOption('url') ? $input->getOption('url') : '';

      $this
        ->setPhpunitPhpEnv($doc, 'BROWSERTEST_OUTPUT_DIRECTORY', "$projectRootDirAbs/reports/human/browser_output")
        ->setPhpunitPhpEnv($doc, 'BROWSERTEST_OUTPUT_BASE_URL', "file://$projectRootDirAbs/reports/human/browser_output")
        ->setPhpunitPhpEnv($doc, 'DTT_BASE_URL', $url)
        ->setPhpunitPhpEnv($doc, 'SIMPLETEST_BASE_URL', $url);

      $this->fs->dumpFile($dst, $doc->saveXML());

      return 0;
    };
  }

  protected function setPhpunitPhpEnv(\DOMDocument $doc, string $name, string $value) {
    $xpath = new \DOMXPath($doc);

    /** @var \DOMElement $root */
    $root = $xpath->query('/phpunit')->item(0);
    $elements = $xpath->query('./php', $root);
    if ($elements->count()) {
      $elementPhp = $elements->item(0);
    }
    else {
      $elementPhp = $doc->createElement('php');
      $root->appendChild($elementPhp);
    }

    $query = sprintf('./env[@name = "%s"]', $name);
    $elements = $xpath->query($query, $elementPhp);
    if ($elements->count()) {
      $elementEnv = $elements->item(0);
    }
    else {
      $elementEnv = $doc->createElement('env');
      $elementEnv->setAttribute('name', $name);
      $elementPhp->appendChild($elementEnv);
    }

    $elementEnv->setAttribute('value', $value);

    return $this;
  }

}
