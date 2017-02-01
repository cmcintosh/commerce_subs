<?php

namespace Drupal\commerce_sub\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;
use Drupal\Core\Entity\EntityStorageInterface;

/**
 * Defines the sub attribute entity class.
 *
 * @ConfigEntityType(
 *   id = "commerce_sub_attribute",
 *   label = @Translation("Sub attribute"),
 *   label_singular = @Translation("sub attribute"),
 *   label_plural = @Translation("sub attributes"),
 *   label_count = @PluralTranslation(
 *     singular = "@count sub attribute",
 *     plural = "@count sub attributes",
 *   ),
 *   handlers = {
 *     "access" = "Drupal\commerce\EntityAccessControlHandler",
 *     "permission_provider" = "Drupal\commerce\EntityPermissionProvider",
 *     "list_builder" = "Drupal\commerce_sub\SubAttributeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\commerce_sub\Form\SubAttributeForm",
 *       "edit" = "Drupal\commerce_sub\Form\SubAttributeForm",
 *       "delete" = "Drupal\commerce_sub\Form\SubAttributeDeleteForm",
 *     },
 *     "route_provider" = {
 *       "default" = "Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "commerce_sub_attribute",
 *   admin_permission = "administer commerce_sub_attribute",
 *   bundle_of = "commerce_sub_attribute_value",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "elementType"
 *   },
 *   links = {
 *     "add-form" = "/admin/commerce/sub-attributes/add",
 *     "edit-form" = "/admin/commerce/sub-attributes/manage/{commerce_sub_attribute}",
 *     "delete-form" = "/admin/commerce/sub-attributes/manage/{commerce_sub_attribute}/delete",
 *     "collection" =  "/admin/commerce/sub-attributes",
 *   }
 * )
 */
class SubAttribute extends ConfigEntityBundleBase implements SubAttributeInterface {

  /**
   * The attribute ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The attribute label.
   *
   * @var string
   */
  protected $label;

  /**
   * The attribute element type.
   *
   * @var string
   */
  protected $elementType = 'select';

  /**
   * {@inheritdoc}
   */
  public function getValues() {
    $storage = $this->entityTypeManager()->getStorage('commerce_sub_attribute_value');
    return $storage->loadByAttribute($this->id());
  }

  /**
   * {@inheritdoc}
   */
  public function getElementType() {
    return $this->elementType;
  }

  /**
   * {@inheritdoc}
   */
  public static function postDelete(EntityStorageInterface $storage, array $entities) {
    /** @var \Drupal\commerce_sub\Entity\SubAttributeInterface[] $entities */
    parent::postDelete($storage, $entities);

    // Delete all associated values.
    $values = [];
    foreach ($entities as $entity) {
      foreach ($entity->getValues() as $value) {
        $values[$value->id()] = $value;
      }
    }
    /** @var \Drupal\Core\Entity\EntityStorageInterface $value_storage */
    $value_storage = \Drupal::service('entity_type.manager')->getStorage('commerce_sub_attribute_value');
    $value_storage->delete($values);
  }

}
