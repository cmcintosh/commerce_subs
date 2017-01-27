<?php

namespace Drupal\commerce_sub;

use Drupal\Core\Entity\ContentEntityStorageInterface;

/**
 * Defines the interface for sub attribute value storage.
 */
interface SubAttributeValueStorageInterface extends ContentEntityStorageInterface {

  /**
   * Loads sub attribute values for the given sub attribute.
   *
   * @param string $attribute_id
   *   The sub attribute ID.
   *
   * @return \Drupal\commerce_sub\Entity\SubAttributeValueInterface[]
   *   The sub attribute values, indexed by id, ordered by weight.
   */
  public function loadByAttribute($attribute_id);

}
