<?php

namespace Drupal\viewsreference\Plugin\Field\FieldWidget;

use Drupal\views\Views;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;

/**
 * Trait for shared code in Viewsreference Field Widgets.
 */
trait ViewsReferenceTrait {

  /**
   * Build the field element.
   */
  public function fieldElement($element, $items, $delta) {

    switch ($element['target_id']['#type']) {

      case 'select':
        $test = array('!value' => '_none');
        $event = 'change';
        break;

      default:
        $test = array('filled' => TRUE);
        $event = 'viewsreference-select';
        break;

    }

    $field_name = $items->getName();
    $name = $field_name . '[' . $delta . '][target_id]';

    $element['target_id']['#target_type'] = 'view';

    $element['target_id']['#ajax'] = array(
      'callback' => array($this, 'getDisplayIds'),
      'event' => $event,
      'progress' => array(
        'type' => 'throbber',
        'message' => t('Getting display Ids...'),
      ),
    );

    $default_value = isset($items[$delta]->getValue()['display_id']) ? $items[$delta]->getValue()['display_id'] : '';
    if ($default_value == '') {
      $options = $this->getAllViewsDisplayIds();
    }
    else {
      $options = $this->getViewDisplayIds($items[$delta]->getValue()['target_id']);
    }

    // We build a unique class name from field elements and any parent elements that might exist
    // Which will be used to render the display id options in our ajax function.
    $class = !empty($element['target_id']['#field_parents']) ? implode('-',
        $element['target_id']['#field_parents']) . '-' : '';
    $class .= $field_name . '-' . $delta . '-display-id';

    $element['display_id'] = array(
      '#title' => 'Display Id',
      '#type' => 'select',
      '#options' => $options,
      '#default_value' => $default_value,
      '#weight' => 10,
      '#attributes' => array(
        'class' => array(
          $class,
        ),
      ),
      '#states' => array(
        'visible' => array(
          ':input[name="' . $name . '"]' => $test,
        ),
      ),
    );

    $element['title'] = array(
      '#title' => 'Include View Title',
      '#type' => 'checkbox',
      '#default_value' => isset($items[$delta]->getValue()['title']) ? $items[$delta]->getValue()['title'] : '',
      '#weight' => 20,
      '#states' => array(
        'visible' => array(
          ':input[name="' . $name . '"]' => $test,
        ),
      ),
    );

    $element['argument'] = array(
      '#title' => 'Argument',
      '#type' => 'textfield',
      '#default_value' => isset($items[$delta]->getValue()['argument']) ? $items[$delta]->getValue()['argument'] : '',
      '#weight' => 21,
      '#states' => array(
        'visible' => array(
          ':input[name="' . $name . '"]' => $test,
        ),
      ),
    );

    $element['#attached']['library'][] = 'viewsreference/viewsreference';

    return $element;
  }

  /**
   * AJAX function to get display IDs for a particular View.
   */
  public function getDisplayIds(array &$form, FormStateInterface $form_state) {

    $trigger = $form_state->getTriggeringElement();
    $delta = $trigger['#delta'];
    $field_name = $trigger['#parents'][0];
    $values = $form_state->getValues();
    $parents = $trigger['#parents'];
    array_shift($parents);

    // Get the value for the target id of the View.
    switch ($trigger['#type']) {
      case 'select':
        $entity_id = $this->getSelectEntityId($values[$field_name], $parents);
        break;

      default:
        $entity_id = $this->getEntityId($values[$field_name], $parents);
    }

    // The following is relevant if our field is nested inside other fields, eg paragraph or field collection.
    if (count($parents) > 2) {
      $field_name = $parents[count($parents) - 3];
    }

    // Obtain the display ids for the given View.
    $options = $this->getViewDisplayIds($entity_id);
    // We recreate the same unique class as in the parent function.
    $class = !empty($trigger['#field_parents']) ? implode('-',
        $trigger['#field_parents']) . '-' : '';
    $element_class = '.' . $class . $field_name . '-' . $delta .
      '-display-id';

    // Construct the html.
    $html = '<optgroup>';
    foreach ($options as $key => $option) {
      $html .= '<option value="' . $key . '">' . $option . '</option>';
    }
    $html .= '</optgroup>';
    $response = new AjaxResponse();
    $response->addCommand(new HtmlCommand($element_class, render($html)));
    return $response;
  }

  /**
   * Helper function to get the current entity_id value from the values array based on parent array.
   *
   * @param array $values
   *   Field array.
   * @param array $parents
   *   Element parents.
   *
   * @return array|bool
   *   The entity id.
   */
  protected function getEntityId($values, $parents) {
    $key = array_shift($parents);
    $values = $values[$key];
    if (is_array($values)) {
      $values = $this->getEntityId($values, $parents);
    }
    return $values;

  }

  /**
   * Helper function to get the current entity_id value from the values array based on:
   * Parent array for select element.
   * Select adds an extra array level.
   *
   * @param array $values
   *   Field array.
   * @param array $parents
   *   Element parents.
   *
   * @return array|bool
   *   The entity ID
   */
  protected function getSelectEntityId(array $values, array $parents) {
    $_parents = $parents;
    $key = array_shift($_parents);

    if (count($parents) > 2) {
      $parents = (array_slice($parents, -2, 2));
    }
    while ($this->arrayDepth($values[$key]) > 2) {
      $values = $values[$key];
      $key = array_shift($_parents);
    }

    return $this->getEntityId($values[$key], $parents);
  }

  /**
   * Helper function to return array depth.
   */
  private function arrayDepth(array $array) {
    $max_depth = 1;

    foreach ($array as $value) {
      if (is_array($value)) {
        $depth = $this->arrayDepth($value) + 1;

        if ($depth > $max_depth) {
          $max_depth = $depth;
        }
      }
    }

    return $max_depth;
  }

  /**
   * Helper function to get all display ids.
   */
  protected function getAllViewsDisplayIds() {
    $views = Views::getAllViews();
    $options = array();
    foreach ($views as $view) {
      foreach ($view->get('display') as $display) {
        $options[$display['id']] = $display['display_title'];
      }
    }
    return $options;
  }

  /**
   * Helper to get display ids for a particular View.
   */
  protected function getViewDisplayIds($entity_id) {
    $views = Views::getAllViews();
    $options = array();
    $view_plugins = $this->getFieldSetting('plugin_types');
    foreach ($views as $view) {
      if ($view->get('id') == $entity_id) {
        foreach ($view->get('display') as $display) {
          if (in_array($display['display_plugin'], $view_plugins)) {
            $options[$display['id']] = $display['display_title'];
          }
        }
      }
    }
    return $options;
  }

  /**
   * Helper to convert view array of machine names to label names.
   */
  protected function getViewNames($views_array) {
    foreach ($views_array as $key => $value) {
      $view = Views::getView($key);
      $views_list[$view->storage->id()] = $view->storage->label();
    }
    return $views_list;
  }

}
