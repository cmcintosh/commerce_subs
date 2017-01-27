<?php

namespace Drupal\commerce_sub\Entity;

use Drupal\commerce\Entity\CommerceBundleEntityInterface;

/**
 * Defines the interface for sub variation types.
 */
interface SubVariationTypeInterface extends CommerceBundleEntityInterface {

  /**
   * Gets the sub variation type's order item type ID.
   *
   * Used for finding/creating the appropriate order item when purchasing a
   * sub (adding it to an order).
   *
   * @return string
   *   The order item type ID.
   */
  public function getOrderItemTypeId();

  /**
   * Sets the sub variation type's order item type ID.
   *
   * @param string $order_item_type_id
   *   The order item type ID.
   *
   * @return $this
   */
  public function setOrderItemTypeId($order_item_type_id);

  /**
   * Gets whether the sub variation title should be automatically generated.
   *
   * @return bool
   *   Whether the sub variation title should be automatically generated.
   */
  public function shouldGenerateTitle();

  /**
   * Sets whether the sub variation title should be automatically generated.
   *
   * @param bool $generate_title
   *   Whether the sub variation title should be automatically generated.
   *
   * @return $this
   */
  public function setGenerateTitle($generate_title);

}
