<?php

namespace Drupal\commerce_sub\Entity;

use Drupal\commerce\Entity\CommerceBundleEntityBase;

/**
 * Defines the sub type entity class.
 *
 * @ConfigEntityType(
 *   id = "commerce_sub_type",
 *   label = @Translation("Sub type"),
 *   label_singular = @Translation("sub type"),
 *   label_plural = @Translation("sub types"),
 *   label_count = @PluralTranslation(
 *     singular = "@count sub type",
 *     plural = "@count sub types",
 *   ),
 *   handlers = {
 *     "list_builder" = "Drupal\commerce_sub\SubTypeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\commerce_sub\Form\SubTypeForm",
 *       "edit" = "Drupal\commerce_sub\Form\SubTypeForm",
 *       "delete" = "Drupal\commerce_sub\Form\SubTypeDeleteForm"
 *     },
 *     "route_provider" = {
 *       "default" = "Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "commerce_sub_type",
 *   admin_permission = "administer commerce_sub_type",
 *   bundle_of = "commerce_sub",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "description",
 *     "variationType",
 *     "injectVariationFields",
 *     "traits",
 *   },
 *   links = {
 *     "add-form" = "/admin/commerce/config/sub-types/add",
 *     "edit-form" = "/admin/commerce/config/sub-types/{commerce_sub_type}/edit",
 *     "delete-form" = "/admin/commerce/config/sub-types/{commerce_sub_type}/delete",
 *     "collection" = "/admin/commerce/config/sub-types"
 *   }
 * )
 */
class SubType extends CommerceBundleEntityBase implements SubTypeInterface {

  /**
   * The sub type description.
   *
   * @var string
   */
  protected $description;

  /**
   * The variation type ID.
   *
   * @var string
   */
  protected $variationType;

  /**
   * Indicates if variation fields should be injected.
   *
   * @var bool
   */
  protected $injectVariationFields = TRUE;

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return $this->description;
  }

  /**
   * {@inheritdoc}
   */
  public function setDescription($description) {
    $this->description = $description;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getVariationTypeId() {
    return $this->variationType;
  }

  /**
   * {@inheritdoc}
   */
  public function setVariationTypeId($variation_type_id) {
    $this->variationType = $variation_type_id;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function shouldInjectVariationFields() {
    return $this->injectVariationFields;
  }

  /**
   * {@inheritdoc}
   */
  public function setInjectVariationFields($inject) {
    $this->injectVariationFields = (bool) $inject;
    return $this;
  }

}
