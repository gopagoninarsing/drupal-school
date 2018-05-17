<?php

namespace Drupal\sch_attendance\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;

/**
 * Class attendanceController.
 *
 *  Returns responses for Attendance routes.
 */
class attendanceController extends ControllerBase {

  public function attendance_list() {
      //place the table in the form
    $data = $this->prepare_attendance_data();
    $result['table'] = array(
      '#type' => 'table',
      '#header' => $data['header'],
      '#rows' => $data['rows'],
      '#attributes' => array(
        'id' => 'bd-contact-table',
      )
    );
    return $result;
  }

  public function prepare_attendance_data($month = null, $year = null) {
    $month = isset($month)?$month:date('m');
    $year = isset($year)?$year:date('Y');
    $days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    $header = ['SNO', 'Student'];

    $userclass = getUserClass();

    $data = $this->getAttendance($userclass, $month, $year);
    // for($count = 1; $count <= $days; $count++) {
    //   $header[] = $count."-".$month."-".$year;
    // }
    return ['header' => $data['header'], 'rows' => $data['rows']];
  }

  public function getAttendance($class, $month, $year) {
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
}
