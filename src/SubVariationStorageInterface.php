<?php

namespace Drupal\commerce_sub;

use Drupal\commerce_sub\Entity\SubInterface;

/**
 * Defines the interface for sub variation storage.
 */
interface SubVariationStorageInterface {

  /**
   * Loads the variation from context.
   *
   * Uses the variation specified in the URL (?v=) if it's active and
   * belongs to the current sub.
   *
   * Note: The returned variation is not guaranteed to be enabled, the caller
   * needs to check it against the list from loadEnabled().
   *
   * @param \Drupal\commerce_sub\Entity\SubInterface $sub
   *   The current sub.
   *
   * @return \Drupal\commerce_sub\Entity\SubVariationInterface
   *   The sub variation.
   */
  public function loadFromContext(SubInterface $sub);

  /**
   * Loads the enabled variations for the given sub.
   *
   * Enabled variations are active variations that have been filtered through
   * the FILTER_VARIATIONS event.
   *
   * @param \Drupal\commerce_sub\Entity\SubInterface $sub
   *   The sub.
   *
   * @return \Drupal\commerce_sub\Entity\SubVariationInterface[]
   *   The enabled variations.
   */
  public function loadEnabled(SubInterface $sub);

}
