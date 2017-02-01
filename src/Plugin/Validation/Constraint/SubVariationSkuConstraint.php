<?php

namespace Drupal\commerce_sub\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Ensures sub variation SKU uniqueness.
 *
 * @Constraint(
 *   id = "SubVariationSku",
 *   label = @Translation("The SKU of the sub variation.", context = "Validation")
 * )
 */
class SubVariationSkuConstraint extends Constraint {

  public $message = 'The SKU %sku is already in use and must be unique.';

}
