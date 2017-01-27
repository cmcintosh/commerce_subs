<?php

namespace Drupal\commerce_sub\ConfigTranslation;

use Drupal\config_translation\ConfigEntityMapper;

/**
 * Provides a configuration mapper for sub attributes.
 */
class SubAttributeMapper extends ConfigEntityMapper {

  /**
   * {@inheritdoc}
   */
  public function getAddRoute() {
    $route = parent::getAddRoute();
    $route->setDefault('_form', '\Drupal\commerce_sub\Form\SubAttributeTranslationAddForm');
    return $route;
  }

  /**
   * {@inheritdoc}
   */
  public function getEditRoute() {
    $route = parent::getEditRoute();
    $route->setDefault('_form', '\Drupal\commerce_sub\Form\SubAttributeTranslationEditForm');
    return $route;
  }

}
