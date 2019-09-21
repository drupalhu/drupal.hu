<?php

declare(strict_types = 1);

namespace Drupal\app_core;

use Drupal\Core\Url;

interface DrupalProjectNameLinkerInterface {

  public function getUrls(): array;

  public function getUrlOptions(): array;

  public function getSafeProjectName(string $projectName): string;

  public function getLink(string $url, string $projectName): array;

  public function getUrl(string $linkTo, string $projectName): Url;

}
