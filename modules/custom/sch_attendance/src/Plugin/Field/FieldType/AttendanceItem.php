<?php

namespace Drupal\sch_attendance\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;


/**
 * Plugin implementation of the 'attendance' field type.
 *
 * @FieldType(
 *   id = "attendance",
 *   label = @Translation("Attendance"),
 *   description = @Translation("This field stores a user attendance in the database."),
 *   default_widget = "attendancewidget",
 *   default_formatter = "attendanceformatter"
 * )
 */
class AttendanceItem extends FieldItemBase {
  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return [
      'columns' => [
        'student' => [
          'type' => 'int'
        ],
        'is_present' => [
          'type' => 'char'
        ],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties['is_present'] = DataDefinition::create('boolean')
      ->setLabel(t('Is Present'));
    $properties['student'] = DataDefinition::create('integer')
      ->setLabel(t('Student'));
    return $properties;
  }

  /**
  * {@inheritdoc}
  */
  public function isEmpty() {
    $isEmpty =
      empty($this->get('student')->getValue()) &&
      empty($this->get('is_present')->getValue());
    return $isEmpty;
  }

  public function setValue($values, $notify = TRUE) {
    parent::setValue($values, $notify);
  }

}
