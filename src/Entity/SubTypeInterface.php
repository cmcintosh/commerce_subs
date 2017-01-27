<?php

namespace Drupal\commerce_sub\Entity;

use Drupal\commerce\Entity\CommerceBundleEntityInterface;
use Drupal\Core\Entity\EntityDescriptionInterface;

/**
 * Defines the interface for sub types.
 */
interface SubTypeInterface extends CommerceBundleEntityInterface, EntityDescriptionInterface {

  /**
   * Gets the sub type's matching variation type ID.
   *
   * @return string
   *   The variation type ID.
   */
  public function getVariationTypeId();

  /**
   * Sets the sub type's matching variation type ID.
   *
   * @param string $variation_type_id
   *   The variation type ID.
   *
   * @return $this
   */
  public function setVariationTypeId($variation_type_id);

  /**
   * Gets whether variation fields should be injected into the rendered sub.
   *
   * @return bool
   *   TRUE if the variation fields should be injected into the rendered
   *   sub, FALSE otherwise.
   */
  public function shouldInjectVariationFields();

  /**
   * Sets whether variation fields should be injected into the rendered sub.
   *
   * @param bool $inject
   *   Whether variation fields should be injected into the rendered sub.
   *
   * @return $this
   */
  public function setInjectVariationFields($inject);

}
