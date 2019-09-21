<?php

declare(strict_types = 1);

namespace Drupal\app_core\Plugin\Field\FieldFormatter;

use Drupal\app_core\DrupalProjectNameLinkerInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\EntityReferenceFormatterBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @\Drupal\Core\Field\Annotation\FieldFormatter(
 *   id = "app_drupal_project_link",
 *   label = @\Drupal\Core\Annotation\Translation("App - Drupal project link"),
 *   description = @\Drupal\Core\Annotation\Translation("Converts a Drupal
 *   project name to a link"), field_types = {
 *     "entity_reference"
 *   }
 * )
 *
 * // @todo Only for Content entities (isApplicable).
 */
class DrupalProjectLinkFieldFormatter extends EntityReferenceFormatterBase implements ContainerFactoryPluginInterface {

  /**
   * @var \Drupal\app_core\DrupalProjectNameLinkerInterface
   */
  protected $linker;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      $container->get('app.drupal_project_linker')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function __construct(
    $plugin_id,
    $plugin_definition,
    FieldDefinitionInterface $field_definition,
    array $settings,
    $label,
    $view_mode,
    array $third_party_settings,
    DrupalProjectNameLinkerInterface $linker
  ) {
    $this->linker = $linker;

    parent::__construct(
      $plugin_id,
      $plugin_definition,
      $field_definition,
      $settings,
      $label,
      $view_mode,
      $third_party_settings
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'link_to' => 'home',
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);

    $form['link_to'] = [
      '#type' => 'select',
      '#required' => TRUE,
      '#title' => $this->t('Link to'),
      '#default_value' => $this->getSetting('link_to'),
      '#options' => $this->linker->getUrlOptions(),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = parent::settingsSummary();

    $linkTo = $this->getSetting('link_to');
    $urls = $this->linker->getUrlOptions();
    $summary['link_to'] = $this->t('Link to: @name', ['@name' => $urls[$linkTo] ?? $urls['home']]);

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    $linkTo = (string) $this->getSetting('link_to');
    /** @var \Drupal\Core\Field\EntityReferenceFieldItemListInterface $items */
    /** @var \Drupal\Core\Entity\ContentEntityInterface $entity */
    foreach ($items->referencedEntities() as $delta => $entity) {
      $elements[$delta] = $this->linker->getLink($linkTo, $entity->label());
      $elements[$delta]['#cache']['tags'] = $entity->getCacheTags();
    }

    return $elements;
  }

}
