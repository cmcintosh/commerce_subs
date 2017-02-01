<?php

namespace Drupal\commerce_sub\Event;

use Drupal\commerce_sub\Entity\SubVariationInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Defines the sub variation event.
 *
 * @see \Drupal\commerce_sub\Event\SubEvents
 */
class SubVariationEvent extends Event {

  /**
   * The sub variation.
   *
   * @var \Drupal\commerce_sub\Entity\SubVariationInterface
   */
  protected $subVariation;

  /**
   * Constructs a new SubVariationEvent.
   *
   * @param \Drupal\commerce_sub\Entity\SubVariationInterface $sub_variation
   *   The sub variation.
   */
  public function __construct(SubVariationInterface $sub_variation) {
    $this->subVariation = $sub_variation;
  }

  /**
   * Gets the sub variation.
   *
   * @return \Drupal\commerce_sub\Entity\SubVariationInterface
   *   The sub variation.
   */
  public function getSubVariation() {
    return $this->subVariation;
  }

}
