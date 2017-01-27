<?php

namespace Drupal\commerce_sub\Event;

use Drupal\commerce_sub\Entity\SubInterface;
use Symfony\Component\EventDispatcher\Event;

class FilterVariationsEvent extends Event {

  /**
   * The parent sub.
   *
   * @var \Drupal\commerce_sub\Entity\SubInterface
   */
  protected $sub;

  /**
   * The enabled variations.
   *
   * @var array
   */
  protected $variations;

  /**
   * Constructs a new FilterVariationsEvent object.
   *
   * @param \Drupal\commerce_sub\Entity\SubInterface $sub
   *   The sub.
   * @param array $variations
   *   The enabled variations.
   */
  public function __construct(SubInterface $sub, array $variations) {
    $this->sub = $sub;
    $this->variations = $variations;
  }

  /**
   * Sets the enabled variations.
   *
   * @param array $variations
   *   The enabled variations.
   */
  public function setVariations(array $variations) {
    $this->variations = $variations;
  }

  /**
   * Gets the enabled variations.
   *
   * @return array
   *   The enabled variations.
   */
  public function getVariations() {
    return $this->variations;
  }

}
