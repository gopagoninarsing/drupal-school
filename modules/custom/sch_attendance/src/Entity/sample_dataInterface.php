<?php

namespace Drupal\sch_attendance\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Sample_data entities.
 *
 * @ingroup sch_attendance
 */
interface sample_dataInterface extends ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Sample_data name.
   *
   * @return string
   *   Name of the Sample_data.
   */
  public function getName();

  /**
   * Sets the Sample_data name.
   *
   * @param string $name
   *   The Sample_data name.
   *
   * @return \Drupal\sch_attendance\Entity\sample_dataInterface
   *   The called Sample_data entity.
   */
  public function setName($name);

  /**
   * Gets the Sample_data creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Sample_data.
   */
  public function getCreatedTime();

  /**
   * Sets the Sample_data creation timestamp.
   *
   * @param int $timestamp
   *   The Sample_data creation timestamp.
   *
   * @return \Drupal\sch_attendance\Entity\sample_dataInterface
   *   The called Sample_data entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Sample_data published status indicator.
   *
   * Unpublished Sample_data are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Sample_data is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Sample_data.
   *
   * @param bool $published
   *   TRUE to set this Sample_data to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\sch_attendance\Entity\sample_dataInterface
   *   The called Sample_data entity.
   */
  public function setPublished($published);

}
