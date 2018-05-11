<?php

namespace Drupal\sch_attendance;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Sample_data entity.
 *
 * @see \Drupal\sch_attendance\Entity\sample_data.
 */
class sample_dataAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\sch_attendance\Entity\sample_dataInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished sample_data entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published sample_data entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit sample_data entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete sample_data entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add sample_data entities');
  }

}
