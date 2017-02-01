<?php

namespace Drupal\commerce_sub\Form;

use Drupal\Core\Entity\EntityDeleteForm;

/**
 * Builds the form to delete a sub attribute.
 */
class SubAttributeDeleteForm extends EntityDeleteForm {

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return $this->t('Deleting a sub attribute will delete all of its values. This action cannot be undone.');
  }

}
