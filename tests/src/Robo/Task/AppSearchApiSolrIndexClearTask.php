<?php

declare(strict_types = 1);

namespace DrupalHu\DrupalHu\Tests\Robo\Task;

use Drupal\marvin\Robo\Task\BaseTask as MarvinBaseTask;
use GuzzleHttp\Client as GuzzleHttpClient;
use GuzzleHttp\ClientInterface as GuzzleHttpClientInterface;
use Sweetchuck\Utils\Uri as UtilsUri;

class AppSearchApiSolrIndexClearTask extends MarvinBaseTask {

  protected string $baseUrl = '';

  public function getBaseUrl(): string {
    return $this->baseUrl;
  }

  /**
   * @return $this
   */
  public function setBaseUrl(string $baseUrl) {
    $this->baseUrl = $baseUrl;

    return $this;
  }

  protected array $index = [];

  public function getIndex(): array {
    return $this->index;
  }

  /**
   * @return $this
   */
  public function setIndex(array $index) {
    $this->index = $index;

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setOptions(array $options) {
    parent::setOptions($options);

    if (array_key_exists('baseUrl', $options)) {
      $this->setBaseUrl($options['baseUrl']);
    }

    if (array_key_exists('index', $options)) {
      $this->setIndex($options['index']);
    }

    return $this;
  }

  protected GuzzleHttpClientInterface $httpClient;

  public function __construct(?GuzzleHttpClientInterface $httpClient = NULL) {
    $this->httpClient = $httpClient ?: new GuzzleHttpClient();
    $this->taskName = 'App - Search API Solr - Index - Clear';
  }

  /**
   * {@inheritdoc}
   */
  protected function runHeader() {
    $this->printTaskInfo('Search API index: {indexId}');

    return $this;
  }

  protected function runAction() {
    // @todo Use Solarium.
    $index = $this->getIndex();
    $connectorConfig =& $index['server']['backend_config']['connector_config'];

    $url = UtilsUri::build([
      'scheme' => $connectorConfig['scheme'],
      'host' => $connectorConfig['host'],
      'port' => $connectorConfig['port'],
      'path' => sprintf('/solr/%s/update', urlencode($connectorConfig['core'])),
      'query' => [
        'commit' => 'true',
      ],
    ]);

    (new GuzzleHttpClient())->post(
      $url,
      [
        'headers' => [
          'Content-type' => 'application/xml',
        ],
        'body' => $this->getIndexClearSolrQueryXml(),
      ],
    );

    return $this;
  }

  protected function getIndexClearSolrQueryXml(): string {
    $baseUrlSafe = $this->escapeSolrQueryValue($this->getBaseUrl());
    $indexIdSafe = $this->escapeSolrQueryValue($this->getIndex()['id']);

    return <<< XML
<delete>
    <query>
        site: "$baseUrlSafe"
        AND
        index_id: "$indexIdSafe"
    </query>
</delete>
XML;
  }

  protected function getTaskContext($context = NULL) {
    $context = parent::getTaskContext($context);

    $index = $this->getIndex();
    if (!empty($index['id'])) {
      $context['indexId'] = $index['id'];
    }

    if (!empty($index['server']['id'])) {
      $context['serverId'] = $index['server']['id'];
    }

    return $context;
  }

  protected function escapeSolrQueryValue(string $value):string {
    return addcslashes($value, '"');
  }

}
