<?php

namespace Drupal\Tests\commerce_sub\Functional;

use Drupal\Core\Url;
use Drupal\Tests\commerce\Functional\CommerceBrowserTestBase;

/**
 * Tests sub UI behavior when there are no stores.
 *
 * @group commerce
 */
class SubNoStoreTest extends CommerceBrowserTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'commerce_store',
    'commerce_sub',
  ];

  /**
   * {@inheritdoc}
   */
  protected function getAdministratorPermissions() {
    return array_merge([
      'administer commerce_sub',
      'administer commerce_sub_type',
      'access commerce_sub overview',
    ], parent::getAdministratorPermissions());
  }

  /**
   * Tests creating a sub.
   */
  public function testCreateSub() {
    $this->drupalGet('admin/commerce/subs');
    $this->clickLink('Add sub');

    // Check that the warning is present.
    $session = $this->assertSession();
    $session->pageTextContains("Subs can't be created until a store has been added.");
    $session->linkExists('Add a new store.');
    $session->linkByHrefExists(Url::fromRoute('entity.commerce_store.add_page')->toString());
  }

}
