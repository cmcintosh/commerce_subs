<?php

namespace Drupal\commerce_sub;

use Drupal\commerce\CommerceContentEntityStorage;

/**
 * Defines the sub attribute value storage.
 */
class SubAttributeValueStorage extends CommerceContentEntityStorage implements SubAttributeValueStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function loadByAttribute($attribute_id) {
    $entity_query = $this->getQuery();
    $entity_query->condition('attribute', $attribute_id);
    $entity_query->sort('weight');
    $entity_query->sort('name');
    $result = $entity_query->execute();
    return $result ? $this->loadMultiple($result) : [];
  }

}
