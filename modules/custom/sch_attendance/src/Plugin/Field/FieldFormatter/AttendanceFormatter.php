<?php

namespace Drupal\sch_attendance\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

/**
 * Plugin implementation of the 'dice' formatter.
 *
 * @FieldFormatter (
 *   id = "attendanceformatter",
 *   label = @Translation("Attendance"),
 *   field_types = {
 *     "attendance"
 *   }
 * )
 */
class AttendanceFormatter extends FormatterBase {
  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode = NULL) {
    $elements = array();

    foreach ($items as $delta => $item) {
        $markup = $item->student . 'd' . $item->is_present;
        $elements[$delta] = array(
        '#type' => 'markup',
        '#markup' => $markup,
      );
    }

    return $elements;
  }
}
