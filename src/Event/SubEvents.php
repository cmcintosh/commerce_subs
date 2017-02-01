<?php

namespace Drupal\commerce_sub\Event;

final class SubEvents {

  /**
   * Name of the event fired after loading a sub.
   *
   * @Event
   *
   * @see \Drupal\commerce_sub\Event\SubEvent
   */
  const PRODUCT_LOAD = 'commerce_sub.commerce_sub.load';

  /**
   * Name of the event fired after creating a new sub.
   *
   * Fired before the sub is saved.
   *
   * @Event
   *
   * @see \Drupal\commerce_sub\Event\SubEvent
   */
  const PRODUCT_CREATE = 'commerce_sub.commerce_sub.create';

  /**
   * Name of the event fired before saving a sub.
   *
   * @Event
   *
   * @see \Drupal\commerce_sub\Event\SubEvent
   */
  const PRODUCT_PRESAVE = 'commerce_sub.commerce_sub.presave';

  /**
   * Name of the event fired after saving a new sub.
   *
   * @Event
   *
   * @see \Drupal\commerce_sub\Event\SubEvent
   */
  const PRODUCT_INSERT = 'commerce_sub.commerce_sub.insert';

  /**
   * Name of the event fired after saving an existing sub.
   *
   * @Event
   *
   * @see \Drupal\commerce_sub\Event\SubEvent
   */
  const PRODUCT_UPDATE = 'commerce_sub.commerce_sub.update';

  /**
   * Name of the event fired before deleting a sub.
   *
   * @Event
   *
   * @see \Drupal\commerce_sub\Event\SubEvent
   */
  const PRODUCT_PREDELETE = 'commerce_sub.commerce_sub.predelete';

  /**
   * Name of the event fired after deleting a sub.
   *
   * @Event
   *
   * @see \Drupal\commerce_sub\Event\SubEvent
   */
  const PRODUCT_DELETE = 'commerce_sub.commerce_sub.delete';

  /**
   * Name of the event fired after saving a new sub translation.
   *
   * @Event
   *
   * @see \Drupal\commerce_sub\Event\SubEvent
   */
  const PRODUCT_TRANSLATION_INSERT = 'commerce_sub.commerce_sub.translation_insert';

  /**
   * Name of the event fired after deleting a sub translation.
   *
   * @Event
   *
   * @see \Drupal\commerce_sub\Event\SubEvent
   */
  const PRODUCT_TRANSLATION_DELETE = 'commerce_sub.commerce_sub.translation_delete';

  /**
   * Name of the event fired after changing the sub variation via ajax.
   *
   * Allows modules to add arbitrary ajax commands to the response returned by
   * the add to cart form #ajax callback.
   *
   * @Event
   *
   * @see \Drupal\commerce_sub\Event\SubVariationAjaxChangeEvent
   */
  const PRODUCT_VARIATION_AJAX_CHANGE = 'commerce_sub.commerce_sub_variation.ajax_change';

  /**
   * Name of the event fired after loading a sub variation.
   *
   * @Event
   *
   * @see \Drupal\commerce_sub\Event\SubVariationEvent
   */
  const PRODUCT_VARIATION_LOAD = 'commerce_sub.commerce_sub_variation.load';

  /**
   * Name of the event fired after creating a new sub variation.
   *
   * Fired before the sub variation is saved.
   *
   * @Event
   *
   * @see \Drupal\commerce_sub\Event\SubVariationEvent
   */
  const PRODUCT_VARIATION_CREATE = 'commerce_sub.commerce_sub_variation.create';

  /**
   * Name of the event fired before saving a sub variation.
   *
   * @Event
   *
   * @see \Drupal\commerce_sub\Event\SubVariationEvent
   */
  const PRODUCT_VARIATION_PRESAVE = 'commerce_sub.commerce_sub_variation.presave';

  /**
   * Name of the event fired after saving a new sub variation.
   *
   * @Event
   *
   * @see \Drupal\commerce_sub\Event\SubVariationEvent
   */
  const PRODUCT_VARIATION_INSERT = 'commerce_sub.commerce_sub_variation.insert';

  /**
   * Name of the event fired after saving an existing sub variation.
   *
   * @Event
   *
   * @see \Drupal\commerce_sub\Event\SubVariationEvent
   */
  const PRODUCT_VARIATION_UPDATE = 'commerce_sub.commerce_sub_variation.update';

  /**
   * Name of the event fired before deleting a sub variation.
   *
   * @Event
   *
   * @see \Drupal\commerce_sub\Event\SubVariationEvent
   */
  const PRODUCT_VARIATION_PREDELETE = 'commerce_sub.commerce_sub_variation.predelete';

  /**
   * Name of the event fired after deleting a sub variation.
   *
   * @Event
   *
   * @see \Drupal\commerce_sub\Event\SubVariationEvent
   */
  const PRODUCT_VARIATION_DELETE = 'commerce_sub.commerce_sub_variation.delete';

  /**
   * Name of the event fired after saving a new sub variation translation.
   *
   * @Event
   *
   * @see \Drupal\commerce_sub\Event\SubVariationEvent
   */
  const PRODUCT_VARIATION_TRANSLATION_INSERT = 'commerce_sub.commerce_sub_variation.translation_insert';

  /**
   * Name of the event fired after deleting a sub variation translation.
   *
   * @Event
   *
   * @see \Drupal\commerce_sub\Event\SubVariationEvent
   */
  const PRODUCT_VARIATION_TRANSLATION_DELETE = 'commerce_sub.commerce_sub_variation.translation_delete';

  /**
   * Name of the event fired when filtering variations.
   *
   * @Event
   *
   * @see \Drupal\commerce_sub\Event\FilterVariationsEvent
   */
  const FILTER_VARIATIONS = "commerce_sub.filter_variations";

  /**
  * Name of the event fired when we want to renew the subscription.
  *
  */
  const SUBSCRIPTION_RENEW = 'commerce_subscription.subscription_renew';

  /**
  * Name of the event fired when we want to cancel the subscription.
  */
  const SUBSCRIPTION_CANCEL = 'commerce_subscription.subscription_cancel';
}
