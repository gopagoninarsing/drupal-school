<?php

namespace Drupal\sch_attendance\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\user\Entity\User;

/**
 * Plugin implementation of the 'attendancewidget' widget.
 *
 * @FieldWidget(
 *   id = "attendancewidget",
 *   label = @Translation("Attendance"),
 *   field_types = {
 *     "attendance"
 *   }
 * )
 */
class AttendanceWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'placeholder' => '',
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $element['placeholder'] = [
      '#type' => 'textfield',
      '#title' => t('Placeholder'),
      '#default_value' => $this->getSetting('placeholder'),
      '#description' => t('Text that will be shown inside the field until a value is entered. This hint is usually a sample value or a brief description of the expected format.'),
    ];
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];

    $placeholder = $this->getSetting('placeholder');
    if (!empty($placeholder)) {
      $summary[] = t('Placeholder: @placeholder', ['@placeholder' => $placeholder]);
    }
    else {
      $summary[] = t('No placeholder');
    }

    return $summary;
  }

  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $default_class_id = isset($form['class_id']['widget']['#default_value'][0])?$form['class_id']['widget']['#default_value'][0]:null;
    $class_id = isset($form_state->getValue('class_id')[0]['target_id'])?$form_state->getValue('class_id')[0]['target_id']:$default_class_id;
    //Sch Attendance MOdule Function
    //TODO: Make it a service
    $students = getClassStudents($class_id);

    if ($delta < count($students)) {
      $element['student_name'] = [
        '#type' => 'markup',
        '#markup' => isset($items[$delta]->student) ? $items[$delta]->student : isset($students[$delta])?$students[$delta]['name']: null,
      ];

      $element['student'] = [
        '#type' => 'hidden',
        '#default_value' => isset($items[$delta]->student) ? $items[$delta]->student : isset($students[$delta])?$students[$delta]['id']: null,
      ];

      $element['is_present'] = [
        '#type' => 'checkbox',
        '#title' => t('Is Present'),
        '#default_value' => isset($items[$delta]->is_present) ? $items[$delta]->is_present : null,
        '#empty_value' => '',
      ];
      // If cardinality is 1, ensure a label is output for the field by wrapping
      // it in a details element.
      if ($this->fieldDefinition->getFieldStorageDefinition()->getCardinality() == 1) {
        $element += array(
          '#type' => 'fieldset',
          '#attributes' => array('class' => array('container-inline')),
        );
      }
      return $element;
    }
  }


  /**
   * {@inheritdoc}
   */
  // public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
  //   $element['attendance'] = array(
  //     '#type' => 'table',
  //     '#header' => array(
  //       $this->t('Student'),
  //       $this->t('Is Present'),
  //     ),
  //   );

  //   //$element['attendance_table']['attendance'] = ['#type' => 'details', '#open' => TRUE];
  //   $query = \Drupal::entityQuery('user')
  //     ->condition('status', 1)
  //     ->condition('roles', 'student')
  //     ->execute();
  //   $users = User::loadMultiple($query);
  //   foreach($users as $id => $user) {
  //     $element['attendance'][$id]['student'] = [
  //       '#type' => 'markup',
  //       '#markup' => $user->getDisplayName(),
  //     ];

  //     $element['attendance'][$id]['is_present'] = [
  //       '#type' => 'checkbox',
  //       '#title' => '',
  //     ];

  //   }
  //   return $element;
  // }

  public static function process($element, FormStateInterface $form_state, $form) {
    return parent::process($element, $form_state, $form);
  }

}
