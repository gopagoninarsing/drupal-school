<?php

namespace Drupal\sch_attendance\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form handler for the Attendance entity forms.
 *
 * @internal
 */
class AttendanceForm extends ContentEntityForm {

  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);
    return $form;
  }
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var $entity \Drupal\sch_attendance\Entity\testing */
    $form = parent::buildForm($form, $form_state);
    $ajax = [ 'callback' => '\Drupal\sch_attendance\Form\AttendanceForm::sayHello',
              'event' => 'change',
              'wrapper' => 'student-fields',
              'progress' => [
                              'type' => 'throbber',
                              'message' => t('Updating Students....')
                            ]
            ];
    $form['class_id']['widget']['#ajax'] = $ajax;
    $form['student_id']['#attributes'] = ['id' => ['student-fields']];
    return $form;
  }

  public function sayHello(array &$form, FormStateInterface $form_state) {
    return $form['student_id'];
  }

  // public function testform(array &$form, FormStateInterface $form_state) {
  //   return $form['student_id'];
  // }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = $this->entity;
    $status = parent::save($form, $form_state);

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label .', ['%label' => $entity->label()]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Testing.', ['%label' => $entity->label()]));
    }
    $form_state->setRedirect('entity.sch_attendance.canonical', ['sch_attendance' => $entity->id()]);
  }

}
