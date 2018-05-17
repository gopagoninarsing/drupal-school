<?php

namespace Drupal\sch_attendance\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form handler for the Attendance entity forms.
 *
 * @internal
 */
class AttendanceFilterForm extends FormBase {

  public function getFormId() {
    return 'AttendanceFilterForm';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var $entity \Drupal\sch_attendance\Entity\testing */
    //$form = parent::buildForm($form, $form_state);
    $form["class"] = [
      '#type' => 'textfield',
      '#title' => 'Class',
    ];
    $form['submit'] = [
      '#type' => 'button',
      '#value' => t('Filter'),
    ];
    $form['form_build_id']['#access'] = FALSE;
    $form_state->setMethod('get');
    return $form;
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {
    // Validation is optional.
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Validation is optional.
  }
}
