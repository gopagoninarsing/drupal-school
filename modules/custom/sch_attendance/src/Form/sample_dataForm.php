<?php

namespace Drupal\sch_attendance\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for Sample_data edit forms.
 *
 * @ingroup sch_attendance
 */
class sample_dataForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var $entity \Drupal\sch_attendance\Entity\sample_data */
    $form = parent::buildForm($form, $form_state);

    $entity = $this->entity;

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = $this->entity;

    $status = parent::save($form, $form_state);

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Sample_data.', [
          '%label' => $entity->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Sample_data.', [
          '%label' => $entity->label(),
        ]));
    }
    $form_state->setRedirect('entity.sample_data.canonical', ['sample_data' => $entity->id()]);
  }

}
