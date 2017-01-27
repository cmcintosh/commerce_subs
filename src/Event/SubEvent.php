<?php

namespace Drupal\commerce_sub\Event;

use Drupal\commerce_sub\Entity\SubInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Defines the sub event.
 *
 * @see \Drupal\commerce_sub\Event\SubEvents
 */
class SubEvent extends Event {

  /**
   * The sub.
   *
   * @var \Drupal\commerce_sub\Entity\SubInterface
   */
  protected $sub;

  /**
   * Constructs a new SubEvent.
   *
   * @param \Drupal\commerce_sub\Entity\SubInterface $sub
   *   The sub.
   */
  public function __construct(SubInterface $sub) {
    $this->sub = $sub;
  }

  /**
   * Gets the sub.
   *
   * @return \Drupal\commerce_sub\Entity\SubInterface
   *   The sub.
   */
  public function getSub() {
    return $this->sub;
  }

}
