<?php

namespace Drupal\commerce_sub\Entity;

use Drupal\commerce\Entity\CommerceBundleEntityBase;

/**
 * Defines the sub variation type entity class.
 *
 * @ConfigEntityType(
 *   id = "commerce_sub_variation_type",
 *   label = @Translation("Sub variation type"),
 *   label_singular = @Translation("sub variation type"),
 *   label_plural = @Translation("sub variation types"),
 *   label_count = @PluralTranslation(
 *     singular = "@count sub variation type",
 *     plural = "@count sub variation types",
 *   ),
 *   handlers = {
 *     "list_builder" = "Drupal\commerce_sub\SubVariationTypeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\commerce_sub\Form\SubVariationTypeForm",
 *       "edit" = "Drupal\commerce_sub\Form\SubVariationTypeForm",
 *       "delete" = "Drupal\commerce_sub\Form\SubVariationTypeDeleteForm"
 *     },
 *     "route_provider" = {
 *       "default" = "Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "commerce_sub_variation_type",
 *   admin_permission = "administer commerce_sub_type",
 *   bundle_of = "commerce_sub_variation",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "orderItemType",
 *     "generateTitle",
 *     "traits",
 *   },
 *   links = {
 *     "add-form" = "/admin/commerce/config/sub-variation-types/add",
 *     "edit-form" = "/admin/commerce/config/sub-variation-types/{commerce_sub_variation_type}/edit",
 *     "delete-form" = "/admin/commerce/config/sub-variation-types/{commerce_sub_variation_type}/delete",
 *     "collection" =  "/admin/commerce/config/sub-variation-types"
 *   }
 * )
 */
class SubVariationType extends CommerceBundleEntityBase implements SubVariationTypeInterface {

  /**
   * The order item type ID.
   *
   * @var string
   */
  protected $orderItemType;

  /**
   * Whether the sub variation title should be automatically generated.
   *
   * @var bool
   */
  protected $generateTitle;

  /**
   * {@inheritdoc}
   */
  public function getOrderItemTypeId() {
    return $this->orderItemType;
  }

  /**
   * {@inheritdoc}
   */
  public function setOrderItemTypeId($order_item_type_id) {
    $this->orderItemType = $order_item_type_id;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function shouldGenerateTitle() {
    return (bool) $this->generateTitle;
  }

  /**
   * {@inheritdoc}
   */
  public function setGenerateTitle($generate_title) {
    $this->generateTitle = $generate_title;
  }

}
