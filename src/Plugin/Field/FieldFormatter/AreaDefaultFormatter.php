<?php

namespace Drupal\scc_selector\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\scc_selector\Plugin\Field\FieldType\AreaItem;

/**
 * Plugin implementation of the 'scc_selector_area_default' formatter.
 *
 * @FieldFormatter(
 *   id = "scc_selector_area_default",
 *   label = @Translation("Default"),
 *   field_types = {"scc_selector_area"}
 * )
 */
class AreaDefaultFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array {
    $element = [];

    foreach ($items as $delta => $item) {

      if ($item->state) {
        $allowed_values = AreaItem::allowedStateValues();
        $element[$delta]['state'] = [
          '#type' => 'item',
          '#title' => $this->t('State'),
          '#markup' => $allowed_values[$item->state],
        ];


        if ($item->county) {
          $allowed_values = AreaItem::allowedCountyValues($item->state);
          $element[$delta]['county'] = [
            '#type' => 'item',
            '#title' => $this->t('County'),
            '#markup' => $allowed_values[$item->county],
          ];
        }

        if ($item->city) {
          $element[$delta]['city'] = [
            '#type' => 'item',
            '#title' => $this->t('City'),
            '#markup' => $item->city,
          ];
        }
      }

    }

    return $element;
  }

}
