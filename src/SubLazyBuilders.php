<?php

namespace Drupal\commerce_sub;

use Drupal\Core\Entity\EntityFormBuilderInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Provides #lazy_builder callbacks.
 */
class SubLazyBuilders {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The entity form builder.
   *
   * @var \Drupal\Core\Entity\EntityFormBuilderInterface
   */
  protected $entityFormBuilder;

  /**
   * Constructs a new CartLazyBuilders object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Entity\EntityFormBuilderInterface $entity_form_builder
   *   The entity form builder.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, EntityFormBuilderInterface $entity_form_builder) {
    $this->entityTypeManager = $entity_type_manager;
    $this->entityFormBuilder = $entity_form_builder;
  }

  /**
   * Builds the add to cart form.
   *
   * @param string $sub_id
   *   The sub ID.
   * @param string $view_mode
   *   The view mode used to render the sub.
   * @param bool $combine
   *   TRUE to combine order items containing the same sub variation.
   *
   * @return array
   *   A renderable array containing the cart form.
   */
  public function addToCartForm($sub_id, $view_mode, $combine) {
    /** @var \Drupal\commerce_order\OrderItemStorageInterface $order_item_storage */
    $order_item_storage = $this->entityTypeManager->getStorage('commerce_order_item');

    /** @var \Drupal\commerce_sub\Entity\SubInterface $sub */
    $sub = $this->entityTypeManager->getStorage('commerce_sub')->load($sub_id);
    if ($sub->getDefaultVariation() == null) {
      return [
        '#type' => 'markup',
        '#markup' => t('You currently have an active subscription for this item.')
      ];
    }
    $order_item = $order_item_storage
      ->createFromPurchasableEntity($sub->getDefaultVariation());

    $form_state_additions = [
      'sub' => $sub,
      'view_mode' => $view_mode,
      'settings' => [
        'combine' => $combine,
      ],
    ];
    return $this->entityFormBuilder->getForm($order_item, 'add_to_cart', $form_state_additions);
  }

}
