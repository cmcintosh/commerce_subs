<?php

namespace Drupal\commerce_sub\Entity;

use Drupal\commerce_store\Entity\EntityStoresInterface;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Defines the interface for subs.
 */
interface SubInterface extends ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface, EntityStoresInterface {

  /**
   * Gets the sub title.
   *
   * @return string
   *   The sub title
   */
  public function getTitle();

  /**
   * Sets the sub title.
   *
   * @param string $title
   *   The sub title.
   *
   * @return $this
   */
  public function setTitle($title);

  /**
   * Get whether the sub is published.
   *
   * Unpublished subs are only visible to their authors and administrators.
   *
   * @return bool
   *   TRUE if the sub is published, FALSE otherwise.
   */
  public function isPublished();

  /**
   * Sets whether the sub is published.
   *
   * @param bool $published
   *   Whether the sub is published.
   *
   * @return $this
   */
  public function setPublished($published);

  /**
   * Gets the sub creation timestamp.
   *
   * @return int
   *   The sub creation timestamp.
   */
  public function getCreatedTime();

  /**
   * Sets the sub creation timestamp.
   *
   * @param int $timestamp
   *   The sub creation timestamp.
   *
   * @return $this
   */
  public function setCreatedTime($timestamp);

  /**
   * Gets the variation IDs.
   *
   * @return int[]
   *   The variation IDs.
   */
  public function getVariationIds();

  /**
   * Gets the variations.
   *
   * @return \Drupal\commerce_sub\Entity\SubVariationInterface[]
   *   The variations.
   */
  public function getVariations();

  /**
   * Sets the variations.
   *
   * @param \Drupal\commerce_sub\Entity\SubVariationInterface[] $variations
   *   The variations.
   *
   * @return $this
   */
  public function setVariations(array $variations);

  /**
   * Gets whether the sub has variations.
   *
   * A sub must always have at least one variation, but a newly initialized
   * (or invalid) sub entity might not have any.
   *
   * @return bool
   *   TRUE if the sub has variations, FALSE otherwise.
   */
  public function hasVariations();

  /**
   * Adds a variation.
   *
   * @param \Drupal\commerce_sub\Entity\SubVariationInterface $variation
   *   The variation.
   *
   * @return $this
   */
  public function addVariation(SubVariationInterface $variation);

  /**
   * Removes a variation.
   *
   * @param \Drupal\commerce_sub\Entity\SubVariationInterface $variation
   *   The variation.
   *
   * @return $this
   */
  public function removeVariation(SubVariationInterface $variation);

  /**
   * Checks whether the sub has a given variation.
   *
   * @param \Drupal\commerce_sub\Entity\SubVariationInterface $variation
   *   The variation.
   *
   * @return bool
   *   TRUE if the variation was found, FALSE otherwise.
   */
  public function hasVariation(SubVariationInterface $variation);

  /**
   * Gets the default variation.
   *
   * @return \Drupal\commerce_sub\Entity\SubVariationInterface
   *   The default variation.
   */
  public function getDefaultVariation();

}
