<?php

namespace Drupal\commerce_sub\Event;

use Drupal\commerce_sub\Entity\SubAttributeValueInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Defines the sub attribute value event.
 *
 * @see \Drupal\commerce_sub\Event\SubEvents
 */
class SubAttributeValueEvent extends Event {

  /**
   * The sub attribute value.
   *
   * @var \Drupal\commerce_sub\Entity\SubAttributeValueInterface
   */
  protected $attributeValue;

  /**
   * Constructs a new SubAttributeValueEvent.
   *
   * @param \Drupal\commerce_sub\Entity\SubAttributeValueInterface $attribute_value
   *   The sub attribute value.
   */
  public function __construct(SubAttributeValueInterface $attribute_value) {
    $this->attributeValue = $attribute_value;
  }

  /**
   * Gets the sub attribute value.
   *
   * @return \Drupal\commerce_sub\Entity\SubAttributeValueInterface
   *   The sub attribute value.
   */
  public function getAttributeValue() {
    return $this->attributeValue;
  }

}
