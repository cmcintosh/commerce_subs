<?php

namespace Drupal\commerce_sub\Form;

use Drupal\commerce\EntityTraitManagerInterface;
use Drupal\commerce\Form\CommerceBundleEntityFormBase;
use Drupal\commerce_sub\SubAttributeFieldManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\language\Entity\ContentLanguageSettings;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SubVariationTypeForm extends CommerceBundleEntityFormBase {

  /**
   * The attribute field manager.
   *
   * @var \Drupal\commerce_sub\SubAttributeFieldManagerInterface
   */
  protected $attributeFieldManager;

  /**
   * Constructs a new SubVariationTypeForm object.
   *
   * @param \Drupal\commerce\EntityTraitManagerInterface $trait_manager
   *   The entity trait manager.
   * @param \Drupal\commerce_sub\SubAttributeFieldManagerInterface $attribute_field_manager
   *   The attribute field manager.
   */
  public function __construct(EntityTraitManagerInterface $trait_manager, SubAttributeFieldManagerInterface $attribute_field_manager) {
    parent::__construct($trait_manager);

    $this->attributeFieldManager = $attribute_field_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('plugin.manager.commerce_entity_trait'),
      $container->get('commerce_sub.attribute_field_manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);
    /** @var \Drupal\commerce_sub\Entity\SubVariationTypeInterface $variation_type */
    $variation_type = $this->entity;

    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $variation_type->label(),
      '#required' => TRUE,
    ];
    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $variation_type->id(),
      '#machine_name' => [
        'exists' => '\Drupal\commerce_sub\Entity\SubVariationType::load',
      ],
      '#maxlength' => EntityTypeInterface::BUNDLE_MAX_LENGTH,
    ];
    $form['generateTitle'] = [
      '#type' => 'checkbox',
      '#title' => t('Generate variation titles based on attribute values.'),
      '#default_value' => $variation_type->shouldGenerateTitle(),
    ];
    $form = $this->buildTraitForm($form, $form_state);

    if ($this->moduleHandler->moduleExists('commerce_order')) {
      // Prepare a list of order item types used to purchase sub variations.
      $order_item_type_storage = $this->entityTypeManager->getStorage('commerce_order_item_type');
      $order_item_types = $order_item_type_storage->loadMultiple();
      $order_item_types = array_filter($order_item_types, function ($order_item_type) {
        return $order_item_type->getPurchasableEntityTypeId() == 'commerce_sub_variation';
      });
      $order_item_types = array_map(function ($order_item_type) {
        return $order_item_type->label();
      }, $order_item_types);

      $form['orderItemType'] = [
        '#type' => 'select',
        '#title' => $this->t('Order item type'),
        '#default_value' => $variation_type->getOrderItemTypeId(),
        '#options' => $order_item_types,
        '#empty_value' => '',
        '#required' => TRUE,
      ];
    }

    $used_attributes = [];
    if (!$variation_type->isNew()) {
      $attribute_map = $this->attributeFieldManager->getFieldMap($variation_type->id());
      $used_attributes = array_column($attribute_map, 'attribute_id');
    }
    /** @var \Drupal\commerce_sub\Entity\SubAttributeInterface[] $attributes */
    $attributes = $this->entityTypeManager->getStorage('commerce_sub_attribute')->loadMultiple();
    $attribute_options = array_map(function ($attribute) {
      /** @var \Drupal\commerce_sub\Entity\SubAttributeInterface $attribute */
      return $attribute->label();
    }, $attributes);

    $form['original_attributes'] = [
      '#type' => 'value',
      '#value' => $used_attributes,
    ];
    $form['attributes'] = [
      '#type' => 'checkboxes',
      '#title' => t('Attributes'),
      '#options' => $attribute_options,
      '#default_value' => $used_attributes,
      '#access' => !empty($attribute_options),
    ];
    // Disable options which cannot be unset because of existing data.
    foreach ($used_attributes as $attribute_id) {
      if (!$this->attributeFieldManager->canDeleteField($attributes[$attribute_id], $variation_type->id())) {
        $form['attributes'][$attribute_id] = [
          '#disabled' => TRUE,
        ];
      }
    }

    if ($this->moduleHandler->moduleExists('language')) {
      $form['language'] = [
        '#type' => 'details',
        '#title' => $this->t('Language settings'),
        '#group' => 'additional_settings',
      ];
      $form['language']['language_configuration'] = [
        '#type' => 'language_configuration',
        '#entity_information' => [
          'entity_type' => 'commerce_sub_variation',
          'bundle' => $variation_type->id(),
        ],
        '#default_value' => ContentLanguageSettings::loadByEntityTypeBundle('commerce_sub_variation', $variation_type->id()),
      ];
      $form['#submit'][] = 'language_configuration_element_submit';
    }

    return $this->protectBundleIdElement($form);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $this->validateTraitForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $this->entity->save();
    drupal_set_message($this->t('Saved the %label sub variation type.', ['%label' => $this->entity->label()]));
    $form_state->setRedirect('entity.commerce_sub_variation_type.collection');

    $this->submitTraitForm($form, $form_state);
    $attribute_storage = $this->entityTypeManager->getStorage('commerce_sub_attribute');
    $original_attributes = $form_state->getValue('original_attributes');
    $attributes = array_filter($form_state->getValue('attributes'));
    $selected_attributes = array_diff($attributes, $original_attributes);
    $unselected_attributes = array_diff($original_attributes, $attributes);
    if ($selected_attributes) {
      /** @var \Drupal\commerce_sub\Entity\SubAttributeInterface[] $selected_attributes */
      $selected_attributes = $attribute_storage->loadMultiple($selected_attributes);
      foreach ($selected_attributes as $attribute) {
        $this->attributeFieldManager->createField($attribute, $this->entity->id());
      }
    }
    if ($unselected_attributes) {
      /** @var \Drupal\commerce_sub\Entity\SubAttributeInterface[] $unselected_attributes */
      $unselected_attributes = $attribute_storage->loadMultiple($unselected_attributes);
      foreach ($unselected_attributes as $attribute) {
        $this->attributeFieldManager->deleteField($attribute, $this->entity->id());
      }
    }
  }

}
