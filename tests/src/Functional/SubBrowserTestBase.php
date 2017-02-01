<?php

namespace Drupal\Tests\commerce_sub\Functional;

use Drupal\field\Tests\EntityReference\EntityReferenceTestTrait;
use Drupal\commerce_store\StoreCreationTrait;
use Drupal\Tests\commerce\Functional\CommerceBrowserTestBase;

/**
 * Defines base class for shortcut test cases.
 */
abstract class SubBrowserTestBase extends CommerceBrowserTestBase {

  use EntityReferenceTestTrait;
  use StoreCreationTrait;

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = [
    'commerce_store',
    'commerce_sub',
    'commerce_order',
    'field_ui',
    'options',
    'taxonomy',
  ];

  /**
   * The sub to test against.
   *
   * @var \Drupal\commerce_sub\Entity\SubInterface[]
   */
  protected $sub;

  /**
   * The stores to test against.
   *
   * @var \Drupal\commerce_store\Entity\StoreInterface[]
   */
  protected $stores;

  /**
   * {@inheritdoc}
   */
  protected function getAdministratorPermissions() {
    return array_merge([
      'administer commerce_sub',
      'administer commerce_sub_type',
      'administer commerce_sub fields',
      'administer commerce_sub_variation fields',
      'administer commerce_sub_variation display',
      'access commerce_sub overview',
    ], parent::getAdministratorPermissions());
  }

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->stores = [];
    for ($i = 0; $i < 3; $i++) {
      $this->stores[] = $this->createStore();
    }
  }

}
