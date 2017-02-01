<?php

namespace Drupal\commerce_sub\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\UrlHelper;
// use Drupal\commerce_log\Entity;

class SubCancelConfirmForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'commerce_sub_cancel_form';
  }

  /**
   * {@inheritdoc}
   */
   public function buildForm(array $form, FormStateInterface $form_state, $user = NULL, $commerce_order = NULL) {

     $form['account'] = [
       '#type' => 'value',
       '#value' => $user
     ];

     $form['commerce_order'] = [
      '#type' => 'value',
      '#value' => $commerce_order
     ];

     $form['warning'] = [
       '#type' => 'markup',
       '#markup' => t('By continuing your subscription will be cancelled.')
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

  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $params = Url::fromUri("internal:" . \Drupal::request()->getRequestUri() )->getRouteParameters();
    // For testing puroses atm, just do a db_merge to cancel the subscription
    cancel_subscription($form_state->getValue('commerce_order'));
    $form_state->setRedirect('entity.commerce_order.user_view', $params);

    // If we have the commerce log module enabled, create a entry.
    // $logManager = \Drupal::entityTypeManager()
    //   ->getStorage('commerce_log');
    // $log = CommerceLog::create([
    //
    // ]);
  }

}
