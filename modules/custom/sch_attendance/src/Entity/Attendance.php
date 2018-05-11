<?php

namespace Drupal\sch_attendance\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\EntityTypeInterface;


/**
 * Defines the Testing entity.
 *
 * @ingroup sch_attendance
 *
 * @ContentEntityType(
 *   id = "sch_attendance",
 *   label = @Translation("Attendance"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\sch_attendance\sch_attendanceListBuilder",
 *     "views_data" = "Drupal\sch_attendance\Entity\sch_attendanceViewsData",
 *     "form" = {
 *       "default" = "Drupal\sch_attendance\Form\AttendanceForm",
 *       "add" = "Drupal\sch_attendance\Form\AttendanceForm",
 *       "edit" = "Drupal\sch_attendance\Form\AttendanceForm",
 *     },
 *    "access" = "Drupal\sch_attendance\sch_attendanceAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\sch_attendance\sch_attendanceHtmlRouteProvider",
 *     },
 *   },
 *   admin_permission = "administer attendance entities",
 *   base_table = "sch_attendance",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "created_by" = "created_by",
 *     "class_id" = "class_id"
 *   },
 *   links = {
 *     "canonical" = "/attendance/{sch_attendance}",
 *     "add-form" = "/attendance/add",
 *     "edit-form" = "/attendance/{sch_attendance}/edit",
 *     "collection" = "/attendance",
 *   }
 * )
 */

class Attendance extends ContentEntityBase implements ContentEntityInterface {
  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields['id'] = BaseFieldDefinition::create('integer')
        ->setLabel(t('ID'))
        ->setDescription(t('The ID of the Adendance entity.'))
        ->setReadOnly(TRUE);

    $fields['name'] = BaseFieldDefinition::create('string')
        ->setLabel(t('Attendance Date'))
        ->setSettings([
          'max_length' => 50,
          'text_processing' => 0,
          'disabled' => true,
        ])
        ->setDefaultValue(date('Y-m-d'))
        ->setDisplayOptions('form', [
          'type' => 'string_textfield',
          'weight' => -4,
        ])
        ->setRequired(TRUE);

    $fields['uuid'] = BaseFieldDefinition::create('uuid')
      ->setLabel(t('UUID'))
      ->setDescription(t('The UUID of the Advertiser entity.'))
      ->setReadOnly(TRUE);

    $fields['class_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Class'))
      ->setDescription(t('Class.'))
      ->setSetting('target_type', 'node')
      ->setSetting('handler', 'node:staff_classes')
      ->setSetting('handler_settings',['target_bundles'=>['class'=>'class']])
      ->setDisplayOptions('form', [
        'type' => 'options_select',
        'weight' => 0,
       ]);

    $fields['student_id'] = BaseFieldDefinition::create('attendance')
    ->setLabel(t('Student'))
    ->setCardinality(100)
    ->setDisplayOptions('form', [
      'type' => 'attendance_widget',
    ]);

    $fields['created_by'] = BaseFieldDefinition::create('entity_reference')
    ->setLabel(t('Authored by'))
    ->setSetting('target_type', 'user')
    ->setDisplayOptions('form', [
      'type' => 'entity_reference_autocomplete',
      'weight' => 5,
      'settings' => [
        'match_operator' => 'CONTAINS',
        'size' => '60',
        'placeholder' => '',
      ],
    ]);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    return $fields;
  }
}
