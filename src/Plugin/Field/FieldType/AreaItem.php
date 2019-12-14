<?php

namespace Drupal\scc_selector\Plugin\Field\FieldType;

use Drupal\Component\Utility\Random;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Defines the 'scc_selector_area' field type.
 *
 * @FieldType(
 *   id = "scc_selector_area",
 *   label = @Translation("Area"),
 *   category = @Translation("General"),
 *   default_widget = "scc_selector_area",
 *   default_formatter = "scc_selector_area_default"
 * )
 */
class AreaItem extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    if ($this->state !== NULL) {
      return FALSE;
    }
    elseif ($this->county !== NULL) {
      return FALSE;
    }
    elseif ($this->city !== NULL) {
      return FALSE;
    }
    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {

    $properties['state'] = DataDefinition::create('string')
      ->setLabel(t('State'));
    $properties['county'] = DataDefinition::create('string')
      ->setLabel(t('County'));
    $properties['city'] = DataDefinition::create('string')
      ->setLabel(t('City'));

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {

    $columns = [
      'state' => [
        'type' => 'varchar',
        'length' => 255,
      ],
      'county' => [
        'type' => 'varchar',
        'length' => 255,
      ],
      'city' => [
        'type' => 'varchar',
        'length' => 255,
      ],
    ];

    $schema = [
      'columns' => $columns,
      // @DCG Add indexes here if necessary.
    ];

    return $schema;
  }

  /**
   * {@inheritdoc}
   */
  public static function generateSampleValue(FieldDefinitionInterface $field_definition) {

    $random = new Random();

    $values['state'] = array_rand(self::allowedStateValues());

    $values['county'] = array_rand(self::allowedCountyValues());

    $values['city'] = $random->word(mt_rand(1, 255));

    return $values;
  }

  /**
   * Returns allowed values for 'state' sub-field.
   *
   * @return array
   *   The list of allowed values.
   */
  public static function allowedStateValues() {
    return [
      'ny' => 'New York',
      'ct' => 'Connecticut',
      'nj' => 'Hell On Earth'
    ];
  }

  /**
   * Returns allowed values for 'county' sub-field.
   *
   * @return array
   *   The list of allowed values.
   */
  public static function allowedCountyValues($state = NULL) {
    switch ($state) {
      case 'ny':
        return [
          'stlaw' => 'St Lawrence',
          'frank' => 'Franklin',
          'essex' => 'Essex'
        ];

      case 'ct':
        return [
          'a' => 'a',
          'b' => 'b',
          'c' => 'c',
        ];

      default:
        return [];
    }
  }

}
