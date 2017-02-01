<?php

/**
* @file
* Contains EventHandler for commerce_license.
* - Defines two new referenceable plugin types for the plugin_type field to use.
*/

namespace Drupal\commerce_sub\EventSubscriber;

use Drupal\commerce\Event\CommerceEvents;
use Drupal\commerce\Event\ReferenceablePluginTypesEvent;
use Drupal\commerce_sub\Event\SubEvents;
use Drupal\commerce_sub\Event\SubEvent;
use Symfony\Component\HttpKernal\KernalEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EventHandler implements EventSubscriberInterface {

  /**
  * - Defines the events this handler will respond to.
  */
  public static function getSubscribedEvents() {
    $events[CommerceEvents::REFERENCEABLE_PLUGIN_TYPES] = ['onReferenceablePluginTypes'];
    $events[SubEvents::SUBSCRIPTION_RENEW] = ['onRenewSubscription'];
    $events[SubEvents::SUBSCRIPTION_CANCEL] = ['onCancelSubscription'];
    return $events;
  }

  public function onReferenceablePluginTypes(ReferenceablePluginTypesEvent $event) {
      $plugin_types = $event->getPluginTypes();
      $plugin_types['sub_item']  = t('Subscription Item');
      $plugin_types['sub_term'] = t('Subscription Term');
      $event->setPluginTypes( $plugin_types );
  }

  /**
  * This is called when a term has lapsed and it is time to create a new invoice.
  */
  public function onRenewSubscription(SubEvent $event) {
    // We want to create a new invoice for the customer of the original order.

    // Create the appropriate order items for the subscription order.

    // Apply the adjustment to get the price to match the subscription amount.

    // Charge the customer.
  }

  /**
  * This is called when a user or admin cancels a subcription.
  */
  public function onCancelSubscription(SubEvent $event) { }

}
