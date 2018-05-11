<?php

namespace Drupal\sch_attendance\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Sample_data entities.
 */
class sample_dataViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    // Additional information for Views integration, such as table joins, can be
    // put here.

    return $data;
  }

}
