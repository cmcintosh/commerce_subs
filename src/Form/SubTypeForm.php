<?php

namespace Drupal\commerce_sub\Form;

use Drupal\commerce\EntityTraitManagerInterface;
use Drupal\commerce\Form\CommerceBundleEntityFormBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\language\Entity\ContentLanguageSettings;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SubTypeForm extends CommerceBundleEntityFormBase {

  /**
   * The variation type storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $variationTypeStorage;

  /**
   * The entity field manager.
   *
   * @var \Drupal\Core\Entity\EntityFieldManagerInterface
   */
  protected $entityFieldManager;

  /**
   * Creates a new SubTypeForm object.
   *
   * @param \Drupal\commerce\EntityTraitManagerInterface $trait_manager
   *   The entity trait manager.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Entity\EntityFieldManagerInterface $entity_field_manager
   *   The entity field manager.
   */
  public function __construct(EntityTraitManagerInterface $trait_manager, EntityTypeManagerInterface $entity_type_manager, EntityFieldManagerInterface $entity_field_manager) {
    parent::__construct($trait_manager);

    $this->variationTypeStorage = $entity_type_manager->getStorage('commerce_sub_variation_type');
    $this->entityFieldManager = $entity_field_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('plugin.manager.commerce_entity_trait'),
      $container->get('entity_type.manager'),
      $container->get('entity_field.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);
    /** @var \Drupal\commerce_sub\Entity\SubTypeInterface $sub_type */
    $sub_type = $this->entity;
    $variation_types = $this->variationTypeStorage->loadMultiple();
    $variation_types = array_map(function ($variation_type) {
      return $variation_type->label();
    }, $variation_types);
    // Create an empty sub to get the default status value.
    // @todo Clean up once https://www.drupal.org/node/2318187 is fixed.
    if ($this->operation == 'add') {
      $sub = $this->entityTypeManager->getStorage('commerce_sub')->create(['type' => $sub_type->uuid()]);
    }
    else {
      $sub = $this->entityTypeManager->getStorage('commerce_sub')->create(['type' => $sub_type->id()]);
    }

    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $sub_type->label(),
      '#required' => TRUE,
    ];
    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $sub_type->id(),
      '#machine_name' => [
        'exists' => '\Drupal\commerce_sub\Entity\SubType::load',
      ],
      '#maxlength' => EntityTypeInterface::BUNDLE_MAX_LENGTH,
    ];
    $form['description'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Description'),
      '#description' => $this->t('This text will be displayed on the <em>Add sub</em> page.'),
      '#default_value' => $sub_type->getDescription(),
    ];
    $form['variationType'] = [
      '#type' => 'select',
      '#title' => $this->t('Sub variation type'),
      '#default_value' => $sub_type->getVariationTypeId(),
      '#options' => $variation_types,
      '#required' => TRUE,
      '#disabled' => !$sub_type->isNew(),
    ];
    $form['injectVariationFields'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Inject sub variation fields into the rendered sub.'),
      '#default_value' => $sub_type->shouldInjectVariationFields(),
    ];
    $form['sub_status'] = [
      '#type' => 'checkbox',
      '#title' => t('Publish new subs of this type by default.'),
      '#default_value' => $sub->isPublished(),
    ];
    $form = $this->buildTraitForm($form, $form_state);

    if ($this->moduleHandler->moduleExists('language')) {
      $form['language'] = [
        '#type' => 'details',
        '#title' => $this->t('Language settings'),
        '#group' => 'additional_settings',
      ];
      $form['language']['language_configuration'] = [
        '#type' => 'language_configuration',
        '#entity_information' => [
          'entity_type' => 'commerce_sub',
          'bundle' => $sub_type->id(),
        ],
        '#default_value' => ContentLanguageSettings::loadByEntityTypeBundle('commerce_sub', $sub_type->id()),
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
    $status = $this->entity->save();
    // Update the default value of the status field.
    $sub = $this->entityTypeManager->getStorage('commerce_sub')->create(['type' => $this->entity->id()]);
    $value = (bool) $form_state->getValue('sub_status');
    if ($sub->status->value != $value) {
      $fields = $this->entityFieldManager->getFieldDefinitions('commerce_sub', $this->entity->id());
      $fields['status']->getConfig($this->entity->id())->setDefaultValue($value)->save();
      $this->entityFieldManager->clearCachedFieldDefinitions();
    }
    $this->submitTraitForm($form, $form_state);

    drupal_set_message($this->t('The sub type %label has been successfully saved.', ['%label' => $this->entity->label()]));
    $form_state->setRedirect('entity.commerce_sub_type.collection');
    if ($status == SAVED_NEW) {
      commerce_sub_add_stores_field($this->entity);
      commerce_sub_add_body_field($this->entity);
      commerce_sub_add_variations_field($this->entity);
    }
  }

}
