<?php

namespace Drupal\sch_attendance\Plugin\EntityReferenceSelection;

use Drupal\Core\Entity\Plugin\EntityReferenceSelection\DefaultSelection;

/**
 * Custom node Reference For the User to list
 * the User Specific Classes list.
 *
 * @EntityReferenceSelection(
 *   id = "node:staff_classes",
 *   label = @Translation("Classes"),
 *   entity_types = {"node"},
 *   group = "default",
 *   weight = 2
 * )
 *
 */
class UserClassSelection extends DefaultSelection {

  /**
   * Builds an EntityQuery to get referenceable entities.
   *
   * @param string|null $match
   *   (Optional) Text to match the label against. Defaults to NULL.
   * @param string $match_operator
   *   (Optional) The operation the matching should be done with. Defaults
   *   to "CONTAINS".
   *
   * @return \Drupal\Core\Entity\Query\QueryInterface
   *   The EntityQuery object with the basic conditions and sorting applied to
   *   it.
   */
  protected function buildEntityQuery($match = NULL, $match_operator = 'CONTAINS') {
    $target_type = $this->configuration['target_type'];
    $handler_settings = $this->configuration['handler_settings'];
    $entity_type = $this->entityManager
      ->getDefinition($target_type);
    $query = $this->entityManager
      ->getStorage($target_type)
      ->getQuery();

    // If 'target_bundles' is NULL, all bundles are referenceable, no further
    // conditions are needed.
    if (isset($handler_settings['target_bundles']) && is_array($handler_settings['target_bundles'])) {

      // If 'target_bundles' is an empty array, no bundle is referenceable,
      // force the query to never return anything and bail out early.
      if ($handler_settings['target_bundles'] === []) {
        $query
          ->condition($entity_type
          ->getKey('id'), NULL, '=');
        return $query;
      }
      else {
        $query
          ->condition($entity_type
          ->getKey('bundle'), $handler_settings['target_bundles'], 'IN');
      }
    }
    if (isset($match) && ($label_key = $entity_type
      ->getKey('label'))) {
      $query
        ->condition($label_key, $match, $match_operator);
    }

    //Added Custom Class Teacher Condition
    if (!in_array('administrator', \Drupal::currentUser()->getroles())) {
      $query->condition('field_class_teacher', \Drupal::currentUser()->id());
    }
    // Add entity-access tag.
    $query
      ->addTag($target_type . '_access');

    // Add the Selection handler for system_query_entity_reference_alter().
    $query
      ->addTag('entity_reference');
    $query
      ->addMetaData('entity_reference_selection_handler', $this);

    // Add the sort option.
    if (!empty($handler_settings['sort'])) {
      $sort_settings = $handler_settings['sort'];
      if ($sort_settings['field'] != '_none') {
        $query
          ->sort($sort_settings['field'], $sort_settings['direction']);
      }
    }
    return $query;
  }

}
