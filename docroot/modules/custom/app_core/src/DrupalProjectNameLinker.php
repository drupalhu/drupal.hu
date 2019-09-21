<?php

declare(strict_types = 1);

namespace Drupal\app_core;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;

class DrupalProjectNameLinker implements DrupalProjectNameLinkerInterface {

  use StringTranslationTrait;

  /**
   * @var array
   */
  protected $urls = [];

  protected function initUrls() {
    if (!$this->urls) {
      $this->urls = [
        'home' => [
          'id' => 'home',
          'title' => $this->t('Home page'),
          'url_pattern' => 'https://www.drupal.org/project/{{ name }}',
        ],
        'code_browser' => [
          'id' => 'code_browser',
          'title' => $this->t('Code browser'),
          'url_pattern' => 'https://git.drupalcode.org/project/{{ name }}',
        ],
      ];
    }

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getUrls(): array {
    return $this
      ->initUrls()
      ->urls;
  }

  /**
   * {@inheritdoc}
   */
  public function getUrlOptions(): array {
    $options = [];

    foreach ($this->getUrls() as $id => $url) {
      $options[$id] = $url['title'];
    }

    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function getSafeProjectName(string $projectName): string {
    return preg_replace('/[^a-z0-9_]+/u', '_', mb_strtolower($projectName));
  }

  /**
   * {@inheritdoc}
   */
  public function getLink(string $linkTo, string $projectName): array {
    return [
      '#type' => 'link',
      '#title' => $projectName,
      '#url' => $this->getUrl($linkTo, $projectName),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getUrl(string $linkTo, string $projectName): Url {
    $urls = $this->getUrls();
    $urlPattern = $urls[$linkTo]['url_pattern'] ?? $urls['home']['url_pattern'];

    $replacementPairs = [
      '{{ name }}' => $this->getSafeProjectName($projectName),
    ];

    return Url::fromUri(strtr($urlPattern, $replacementPairs));
  }

}
