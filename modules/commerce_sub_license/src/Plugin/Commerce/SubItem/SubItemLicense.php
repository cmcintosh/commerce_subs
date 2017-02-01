<?php

namespace Drupal\commerce_sub_license\Plugin\Commerce\SubItem;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Condition\ConditionPluginBase;
use Drupal\commerce_sub\Plugin\Commerce\SubItem\SubItemInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\CssCommand;
use Drupal\Core\Ajax\HtmlCommand;

/**
* @file
* - Provides a product item for subscription.
*
* @SubItem(
*  id = "sub_license",
*  label = "License",
*  display_label = "License"
* )
*/
class SubItemLicense extends ConditionPluginBase implements SubItemInterface {


  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'commerce_license_variation' => NULL,
    ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function summary() {
    // @TODO: Return a summary of the items for this item.
    return 'Licenses awarded for this subscription.';
  }

  public function execute() { }

  public function evaluate() { }

  /**
  * {@inheritdoc}
  */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    // @TODO: Build a form for selecting products.
    $variation = ($this->configuration['commerce_license_variation']) ? entity_load('commerce_license_variation', $this->configuration['commerce_license_variation']) : NULL;

    $form['commerce_license_variation']  = [
      '#type' => 'entity_autocomplete',
      '#target_type' => 'commerce_license_variation',
      '#title' => t('Select a License'),
      '#description' => t('Select the license that will be added to the invoice when the Subscription renews.'),
      '#default_value' => $variation
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateConfigurationForm(array &$form = null, FormStateInterface $form_state) {
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form = null, FormStateInterface $form_state) {
    $this->configuration['negate'] = $form_state->getValue('negate');
    if ($form_state->hasValue('context_mapping')) {
      $this->setContextMapping($form_state->getValue('context_mapping'));
    }
  }

}
