<?php

namespace Drupal\commerce_sub\Plugin\Action;

use Drupal\Core\Action\ActionBase;
use Drupal\Core\Session\AccountInterface;

/**
 * Unpublishes a sub.
 *
 * @Action(
 *   id = "commerce_unpublish_sub",
 *   label = @Translation("Unpublish selected sub"),
 *   type = "commerce_sub"
 * )
 */
class UnpublishSub extends ActionBase {

  /**
   * {@inheritdoc}
   */
  public function execute($entity = NULL) {
    /** @var \Drupal\commerce_sub\Entity\SubInterface $entity */
    $entity->setPublished(FALSE);
    $entity->save();
  }

  /**
   * {@inheritdoc}
   */
  public function access($object, AccountInterface $account = NULL, $return_as_object = FALSE) {
    /** @var \Drupal\commerce_sub\Entity\SubInterface $object */
    $access = $object
      ->access('update', $account, TRUE)
      ->andIf($object->status->access('edit', $account, TRUE));

    return $return_as_object ? $access : $access->isAllowed();
  }

}
