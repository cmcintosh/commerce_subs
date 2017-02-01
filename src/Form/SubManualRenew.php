<?php

namespace Drupal\commerce_sub\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\UrlHelper;
// use Drupal\commerce_log\Entity;

class SubManualRenew extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'commerce_sub_cancel_form';
  }

  /**
   * {@inheritdoc}
   */
   public function buildForm(array $form, FormStateInterface $form_state) {

     $form['commerce_order'] = [
       '#type' => 'entity_autocomplete',
       '#target_type' => 'commerce_order',
       '#target_bundle' => 'default',
       '#title' => t('Order'),
     ];

     $form['charge'] = [
       '#type' => 'checkbox',
       '#title' => t('Charge customer')
     ];

     $form['submit'] = [
       '#type' => 'submit',
       '#value' => t('Yes, I understand')
     ];

     return $form;
   }

   /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $query = \Drupal::database()->select('commerce_sub_customer_subscription', 'cs');
    $query->condition('cs.order_id', $form_state->getValue('commerce_order'), '=');
    $query->condition('cs.status', 1, '=');
    $query->fields('cs', ['order_id', 'delta', 'sku', 'subscription_item', 'subscription_term', 'status', 'renew_date']);
    $results = $query->execute()->fetchAllAssoc('order_id');
    
    if(!isset($results[$form_state->getValue('commerce_order')])) {
      $form_state->setErrorByName('commerce_order', t('Invalid order selected.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    // Query and get the subscription data.
    $query = \Drupal::database()->select('commerce_sub_customer_subscription', 'cs');
    $query->condition('cs.order_id', $values['commerce_order'], '=');
    $query->condition('cs.status', 1, '=');
    $query->fields('cs', ['order_id', 'delta', 'sku', 'subscription_item', 'subscription_term', 'status', 'renew_date']);
    $results = $query->execute()->fetchAllAssoc('order_id');
    renew_subscription( (array) $results[$values['commerce_order']], $values['charge']);
    drupal_set_message(t('Manually renewed subscription.'));
  }

}
