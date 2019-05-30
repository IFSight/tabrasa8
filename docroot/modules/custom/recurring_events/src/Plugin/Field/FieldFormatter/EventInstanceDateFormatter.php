<?php

namespace Drupal\recurring_events\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\Plugin\Field\FieldFormatter\EntityReferenceFormatterBase;
use Drupal\Core\Entity\Exception\UndefinedLinkTemplateException;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Url;
use Drupal\Core\Link;

/**
 * Plugin implementation of the 'recurring events eventinstance date' formatter.
 *
 * @FieldFormatter(
 *   id = "recurring_events_eventinstance_date",
 *   label = @Translation("EventInstance Date"),
 *   description = @Translation("Display the date of the referenced eventinstance."),
 *   field_types = {
 *     "entity_reference"
 *   }
 * )
 */
class EventInstanceDateFormatter extends EntityReferenceFormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'link' => TRUE,
      'date_format' => 'F jS, Y h:iA',
      'separator' => ' - ',
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements['link'] = [
      '#title' => t('Link date to the referenced entity'),
      '#type' => 'checkbox',
      '#default_value' => $this->getSetting('link'),
    ];

    $php_date_url = Url::fromUri('https://secure.php.net/manual/en/function.date.php');
    $php_date_link = Link::fromTextAndUrl($this->t('PHP date/time format'), $php_date_url);

    $elements['date_format'] = [
      '#type' => 'textfield',
      '#title' => t('Date Format @link', [
        '@link' => $php_date_link->toString(),
      ]),
      '#required' => TRUE,
      '#default_value' => $this->getSetting('date_format'),
    ];

    $elements['separator'] = [
      '#title' => t('Separator'),
      '#type' => 'textfield',
      '#description' => t('Enter the separator to use between start and end dates.'),
      '#default_value' => $this->getSetting('separator'),
    ];

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    $summary[] = $this->getSetting('link') ? t('Link to the referenced entity') : t('No link');
    $summary[] = t('Format: %format', [
      '%format' => $this->getSetting('date_format'),
    ]);
    $summary[] = t('Separator: %separator', [
      '%separator' => $this->getSetting('separator'),
    ]);
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public static function isApplicable(FieldDefinitionInterface $field_definition) {
    return ($field_definition->getFieldStorageDefinition()->getSetting('target_type') == 'eventinstance');
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];
    $output_as_link = $this->getSetting('link');

    foreach ($this->getEntitiesToView($items, $langcode) as $delta => $entity) {
      $date_string = '';
      $user_timezone = new \DateTimeZone(drupal_get_user_timezone());
      if (!empty($entity->date->start_date) && !empty($entity->date->end_date)) {
        /** @var \Drupal\Core\Datetime\DrupalDateTime $start_date */
        $start_date = $entity->date->start_date;
        /** @var \Drupal\Core\Datetime\DrupalDateTime $end_date */
        $end_date = $entity->date->end_date;

        $start_date->setTimezone($user_timezone);
        $end_date->setTimezone($user_timezone);

        $date = [];
        $date[] = $start_date->format($this->getSetting('date_format'));
        $date[] = $end_date->format($this->getSetting('date_format'));

        $date_string = implode($this->getSetting('separator'), $date);
      }

      // If the link is to be displayed and the entity has a uri, display a
      // link.
      if ($output_as_link && !$entity->isNew()) {
        try {
          $uri = $entity->toUrl();
        }
        catch (UndefinedLinkTemplateException $e) {
          // This exception is thrown by \Drupal\Core\Entity\Entity::urlInfo()
          // and it means that the entity type doesn't have a link template nor
          // a valid "uri_callback", so don't bother trying to output a link for
          // the rest of the referenced entities.
          $output_as_link = FALSE;
        }
      }

      if ($output_as_link && isset($uri) && !$entity->isNew()) {
        $elements[$delta] = [
          '#type' => 'link',
          '#title' => $date_string,
          '#url' => $uri,
          '#options' => $uri->getOptions(),
        ];

        if (!empty($items[$delta]->_attributes)) {
          $elements[$delta]['#options'] += ['attributes' => []];
          $elements[$delta]['#options']['attributes'] += $items[$delta]->_attributes;
          // Unset field item attributes since they have been included in the
          // formatter output and shouldn't be rendered in the field template.
          unset($items[$delta]->_attributes);
        }
      }
      else {
        $elements[$delta] = ['#plain_text' => $date_string];
      }
      $elements[$delta]['#cache']['tags'] = $entity->getCacheTags();
    }

    return $elements;
  }

}
