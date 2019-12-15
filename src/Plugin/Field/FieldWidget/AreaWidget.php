<?php

namespace Drupal\scc_selector\Plugin\Field\FieldWidget;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\scc_selector\Plugin\Field\FieldType\AreaItem;
use Symfony\Component\Validator\ConstraintViolationInterface;

/**
 * Defines the 'scc_selector_area' field widget.
 *
 * @FieldWidget(
 *   id = "scc_selector_area",
 *   label = @Translation("Area"),
 *   field_types = {"scc_selector_area"},
 * )
 */
class AreaWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state): array {

    $parent = $items->getFieldDefinition()->getName();
    $inputs = $form_state->getUserInput();
    if ($inputs) {
      $input = $inputs[$parent][$delta];
      $state_val = $input['state'] ?? ($items[$delta]->state ?? NULL);
    }
    else {
      $state_val = $items[$delta]->state ?? NULL;
    }

    $wrapper = 'area-wrapper-' . $delta;

    $element['state'] = [
      '#type' => 'select',
      '#title' => $this->t('State'),
      '#options' => ['' => $this->t('- None -')] + AreaItem::allowedStateValues(),
      '#default_value' => $state_val,
      '#ajax' => [
        'callback' => [$this, 'updateCounties'],
        'wrapper' => $wrapper,
        'event' => 'change',
        '#limit_validation_errors' => [],
      ],
    ];

    $county_val = $items[$delta]->county ?? NULL;
    $allowedCountyValues = AreaItem::allowedCountyValues($state_val);
    $element['county'] = [
      '#type' => 'select',
      '#title' => $this->t('County'),
      '#options' => ['' => $this->t('- None -')] + $allowedCountyValues,
      '#default_value' => array_key_exists($county_val, $allowedCountyValues) ? $county_val : NULL,
    ];

    $element['city'] = [
      '#type' => 'textfield',
      '#title' => $this->t('City'),
      '#default_value' => $items[$delta]->city ?? NULL,
      '#size' => 20,
    ];

    $element['#theme_wrappers'] = ['container', 'form_element'];
    $element['#attributes']['class'][] = 'container-inline';
    $element['#attributes']['class'][] = 'scc-selector-area-elements';
    $element['#attached']['library'][] = 'scc_selector/scc_selector_area';
    $element['#prefix'] = '<div id="' . $wrapper . '">';
    $element['#suffix'] = '</div>';

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function errorElement(array $element, ConstraintViolationInterface $violation, array $form, FormStateInterface $form_state) {
    return isset($violation->arrayPropertyPath[0]) ? $element[$violation->arrayPropertyPath[0]] : $element;
  }

  /**
   * {@inheritdoc}
   */
  public function massageFormValues(array $values, array $form, FormStateInterface $form_state) {
    foreach ($values as $delta => $value) {
      if ($value['state'] === '') {
        $values[$delta]['state'] = NULL;
      }
      if ($value['county'] === '') {
        $values[$delta]['county'] = NULL;
      }
      if ($value['city'] === '') {
        $values[$delta]['city'] = NULL;
      }
    }
    return $values;
  }

  public function updateCounties(array $form, FormStateInterface $form_state) {
    $triggeringElement = $form_state->getTriggeringElement();
    $parents = array_slice($triggeringElement['#array_parents'], 0, -1);
    return NestedArray::getValue($form, $parents);
  }

}
