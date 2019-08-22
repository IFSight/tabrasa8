<?php

namespace Drupal\recurring_events;

use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Config\ConfigFactory;

/**
 * Provides a listing of eventseries items.
 */
class EventSeriesListBuilder extends EntityListBuilder {

  /**
   * The date formatter service.
   *
   * @var \Drupal\Core\Datetime\DateFormatterInterface
   */
  protected $dateFormatter;

  /**
   * The language manager service.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * The config factory service.
   *
   * @var \Drupal\Core\Config\ConfigFactory
   */
  protected $config;

  /**
   * Constructs a new EventSeriesListBuilder object.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type definition.
   * @param \Drupal\Core\Entity\EntityStorageInterface $storage
   *   The entity storage class.
   * @param \Drupal\Core\Datetime\DateFormatterInterface $date_formatter
   *   The date formatter service.
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   *   The language manager service.
   * @param \Drupal\Core\Config\ConfigFactory $config
   *   The config factory service.
   */
  public function __construct(EntityTypeInterface $entity_type, EntityStorageInterface $storage, DateFormatterInterface $date_formatter, LanguageManagerInterface $language_manager, ConfigFactory $config) {
    parent::__construct($entity_type, $storage);
    $this->dateFormatter = $date_formatter;
    $this->languageManager = $language_manager;
    $this->config = $config;

    $config = $this->config->get('recurring_events.eventseries.config');
    $this->limit = $config->get('limit');
  }

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    return new static(
      $entity_type,
      $container->get('entity.manager')->getStorage($entity_type->id()),
      $container->get('date.formatter'),
      $container->get('language_manager'),
      $container->get('config.factory')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header = [];
    $header += [
      'name' => $this->t('Series Name'),
      'type' => [
        'data' => $this->t('Recur Type'),
      ],
      'instances' => [
        'data' => $this->t('Instances'),
        'class' => [RESPONSIVE_PRIORITY_LOW],
      ],
      'starts' => [
        'data' => $this->t('Series Starts'),
        'class' => [RESPONSIVE_PRIORITY_LOW],
      ],
      'author' => [
        'data' => $this->t('Author'),
        'class' => [RESPONSIVE_PRIORITY_LOW],
      ],
      'status' => $this->t('Status'),
      'changed' => [
        'data' => $this->t('Updated'),
        'class' => [RESPONSIVE_PRIORITY_LOW],
      ],
    ];
    // Enable language column if multiple languages are added.
    if ($this->languageManager->isMultilingual()) {
      $header['language'] = [
        'data' => $this->t('Language'),
        'class' => [RESPONSIVE_PRIORITY_LOW],
      ];
    }
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /** @var \Drupal\recurring_events\EventInterface $entity */
    $row['name']['data'] = [
      '#type' => 'link',
      '#title' => $entity->label(),
      '#url' => $entity->toUrl(),
    ];
    $row['type'] = $entity->recur_type->value;
    $row['instances'] = $entity->getInstanceCount();
    $row['starts'] = $this->t('None');
    $timezone = new \DateTimeZone(drupal_get_user_timezone());
    if (!empty($entity->getSeriesStart())) {
      $config = $this->config->get('recurring_events.eventseries.config');
      $start_date = $entity->getSeriesStart();
      $start_date->setTimezone($timezone);
      $row['starts'] = $start_date->format($config->get('date_format'));
    }
    $row['author']['data'] = [
      '#theme' => 'username',
      '#account' => $entity->getOwner(),
    ];
    $row['status'] = $entity->isPublished() ? $this->t('Published') : $this->t('Unpublished');
    $row['changed'] = $this->dateFormatter->format($entity->getChangedTime(), 'short', '', $timezone->getName());

    if ($this->languageManager->isMultilingual()) {
      $row['language'] = $this->languageManager->getLanguageName($entity->language()->getId());
    }
    return $row + parent::buildRow($entity);
  }

  /**
   * {@inheritdoc}
   */
  protected function getEntityIds() {
    $query = $this->getStorage()->getQuery()
      ->sort('changed', 'DESC');

    // Only add the pager if a limit is specified.
    if ($this->limit) {
      $query->pager($this->limit);
    }
    return $query->execute();
  }

}
