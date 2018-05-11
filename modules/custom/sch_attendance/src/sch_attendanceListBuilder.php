<?php

namespace Drupal\sch_attendance;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of attendance entities.
 *
 * @ingroup sch_attendance
 */
class sch_attendanceListBuilder extends EntityListBuilder {


  public function prepare_attendance_header($month = null, $year = null) {
    $month = isset($month)?$month:date('m');
    $year = isset($year)?$year:date('Y');
    $days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    $header = ['SNO', 'Student'];
    for($count = 1; $count <= $days; $count++) {
      $header[] = $count."-".$month."-".$year;
    }
    return $header;
  }


  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header = $this->prepare_attendance_header();
    return $header + parent::buildHeader();
  }


  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\sch_attendance\Entity\sch_attendance */
    $row['id'] = $entity->id();
    $row['name'] = $entity->label();

    //pm($entity->get('created')->getValue()[0]['value']);
    // $row['name'] = Link::createFromRoute(
    //   $entity->label(),
    //   'entity.attendance.edit_form',
    //   ['attandence' => $entity->id()]
    // );
    return $row + parent::buildRow($entity);
  }

}
