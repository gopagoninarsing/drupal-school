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

  public function getAttendanceData($class, $month, $year) {
    $current_path = \Drupal::request()->query;

    $start_date = strtotime($year."-".$month."-01 00:00:00");
    $end_date =  date("Y-m-t 23:59:59", $start_date);
    $header = ["SNO" => 'SNO', "Student" => 'Student'];

    $query = \Drupal::entityQuery('sch_attendance')
    ->condition('class_id', $class)
    ->condition('created', array($start_date, strtotime($end_date)), 'BETWEEN')
    ->execute();

    $attendances = \Drupal::entityTypeManager()->getStorage('sch_attendance')->loadMultiple($query);

    $row = [];
    $students = getClassStudents($class);
    $count = 0;
    if (count($students) > 0) {
      foreach($students as $student) {
        $row[$student['id']]= [
          'sno' => ++$count,
          'student' => $student['name'],
        ];
        foreach($attendances as $attendance) {
          $date = date('d-m-Y', $attendance->get('created')->getValue()[0]['value']);
          $val = array_search($student['id'], array_column($attendance->get('student_id')->getValue(), 'student'));
          $is_present = $attendance->get('student_id')->getValue()[$val]['is_present'];
          $row[$student['id']][$date] = ($is_present == 1)? 'P': 'A';
          $header[$date] = $date;
        }
      }
    }
    return ['header' => $header, 'rows' => $row];
  }

  public function render() {
    $month = isset($month)?$month:date('m');
    $year = isset($year)?$year:date('Y');
    $userclass = getUserClass();
    $data = $this->getAttendanceData($userclass, $month, $year);

    $build['filter'] = \Drupal::formBuilder()->getForm('Drupal\sch_attendance\Form\AttendanceFilterForm');
    $build['filter']['form_id']['#access'] = FALSE;
    $build['filter']['form_build_id']['#access'] = FALSE;
    $build['filter']['form_token']['#access'] = FALSE;

    $build['table'] = [
      '#type' => 'table',
      '#header' => $data['header'],
      '#title' => $this->t('Attendance'),
      '#empty' => $this->t('There is no @label yet.', ['@label' => $this->entityType->getLabel()]),
      '#cache' => [
        'contexts' => $this->entityType->getListCacheContexts(),
        'tags' => $this->entityType->getListCacheTags(),
      ],
      '#rows' => $data['rows'],
    ];

    // Only add the pager if a limit is specified.
    if ($this->limit) {
      $build['pager'] = [
        '#type' => 'pager',
      ];
    }

    return $build;
  }
}
