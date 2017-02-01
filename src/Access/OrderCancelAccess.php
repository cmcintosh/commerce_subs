<?php

namespace Drupal\commerce_sub\Access;

use Drupal\Core\Routing\Access\AccessInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Url;
use Drupal\Core\Access\AccessResult;

class OrderCancelAccess implements AccessInterface {

  public function access(AccountInterface $account) {

    if ($this->isActive()) {
      $params = Url::fromUri("internal:" . \Drupal::request()->getRequestUri() )->getRouteParameters();
      // Check if we have this as a customer order
      if (!$this->isSubscriptionOrder()) {
        return AccessResult::forbidden();
      }
      
      $order = \Drupal::entityTypeManager()
        ->getStorage('commerce_order')
        ->load($params['commerce_order']);
      $customer = $order->getCustomer();
      $lookingAtOwn = FALSE;
      if ($customer !== null) {
        if ($account->getAccount() !== null) {
          $lookingAtOwn = ($customer->id() == $account->id());
        }
      }
      $admin = $account->hasPermission('access commerce administration pages');
      return AccessResult::allowedIf(($admin || $lookingAtOwn));
    }
    return AccessResult::forbidden();
  }

  private function isActive() {
    $params = Url::fromUri("internal:" . \Drupal::request()->getRequestUri() )->getRouteParameters();
    $entity_type = key($params);
    $query = \Drupal::database()->select('commerce_sub_customer_subscription', 'cs');
    $query->condition('cs.order_id', $params['commerce_order'], '=');
    $query->condition('cs.status', 1, '=');
    $query->fields('cs', ['order_id', 'status']);
    $results = $query->execute()->fetchAllAssoc('order_id');
    return isset($results[$params['commerce_order']]);
  }

  private function isSubscriptionOrder() {
    $params = Url::fromUri("internal:" . \Drupal::request()->getRequestUri() )->getRouteParameters();
    $entity_type = key($params);
    $query = \Drupal::database()->select('commerce_sub_customer_subscription', 'cs');
    $query->condition('cs.order_id', $params['commerce_order'], '=');
    $query->fields('cs', ['order_id', 'status']);
    $results = $query->execute()->fetchAllAssoc('order_id');
    return isset($results[$params['commerce_order']]);
  }

}
