<?php

namespace Drupal\commerce_sub\Plugin\Commerce\SubTerm;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Condition\ConditionPluginBase;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\CssCommand;
use Drupal\Core\Ajax\HtmlCommand;

/**
* @file
* - Provides a relative date for setting term length.
*
* @SubTerm(
*  id = "term_relative_date",
*  label = "Relative Date",
*  display_label = "Relative Date"
* )
*/

class TermRelativeDate extends ConditionPluginBase implements SubTermInterface {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'relative_date' => NULL,
    ] + parent::defaultConfiguration();
  }

  public function summary() {
    // @TODO return the details for the term length
    return '';
  }

  public function evaluate() { }

  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form += parent::buildConfigurationForm($form, $form_state);
    $form['relative_date'] = [
      '#type' => 'textfield',
      '#title' => t('Relative Date'),
      '#description' => t('Enter the date relative to the date the order was completed for this subscription to renew.'),
      '#default_value' => ($this->configuration['relative_date']) ? $this->configuration['relative_date'] : ''
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

  // public function submitConfigurationForm(array &$form = null, FormStateInterface $form_state) {
  //   $values = $form_state->getValues();
  //   $susbscription_items = isset($values['variations']['form']['inline_entity_form']['susbscription_term']) ? $values['variations']['form']['inline_entity_form']['susbscription_term'] : $values['variations']['form']['inline_entity_form']['entities'][0]['form']['susbscription_term'];
  //   $sku = isset($values['variations']['form']['inline_entity_form']['susbscription_term']) ? $values['variations']['form']['inline_entity_form']['sku'][0]['value'] : $values['variations']['form']['inline_entity_form']['entities'][0]['form']['sku'][0]['value'];
  //
  //   // foreach ($susbscription_items as $delta => $susbscription_item) {
  //   //   if (is_array($susbscription_item)) {
  //   //     $data = [
  //   //       'sku' => $sku,
  //   //       'delta' => $delta,
  //   //       'date' => $susbscription_item['target_plugin_configuration']['relative_date']
  //   //     ];
  //   //     db_merge('sub_term_relative_date')
  //   //       ->key(array('sku' => $data['sku'], 'delta' => $delta))
  //   //       ->fields($data)
  //   //       ->execute();
  //   //
  //   //   }
  //   // }
  // }

}
