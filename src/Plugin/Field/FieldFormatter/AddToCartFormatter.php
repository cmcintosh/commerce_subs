<?php

namespace Drupal\commerce_sub\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'commerce_add_to_cart' formatter.
 *
 * @FieldFormatter(
 *   id = "commerce_sub_add_to_cart",
 *   label = @Translation("Add to cart form"),
 *   field_types = {
 *     "entity_reference",
 *   },
 * )
 */
class AddToCartFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'combine' => TRUE,
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);
    $form['combine'] = [
      '#type' => 'checkbox',
      '#title' => t('Combine order items containing the same sub variation.'),
      '#description' => t('The order item type, referenced sub variation, and data from fields exposed on the Add to Cart form must all match to combine.'),
      '#default_value' => $this->getSetting('combine'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    if ($this->getSetting('combine')) {
      $summary[] = $this->t('Combine order items containing the same sub variation.');
    }
    else {
      $summary[] = $this->t('Do not combine order items containing the same sub variation.');
    }

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    return [
      '#lazy_builder' => ['commerce_sub.lazy_builders:addToCartForm', [
        $items->getEntity()->id(),
        $this->viewMode,
        $this->getSetting('combine'),
      ],
      ],
      '#create_placeholder' => TRUE,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public static function isApplicable(FieldDefinitionInterface $field_definition) {
    $has_cart = \Drupal::moduleHandler()->moduleExists('commerce_cart');
    $entity_type = $field_definition->getTargetEntityTypeId();
    $field_name = $field_definition->getName();
    return $has_cart && $entity_type == 'commerce_sub' && $field_name == 'variations';
  }

}
