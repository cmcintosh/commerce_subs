<?php

namespace Drupal\Tests\commerce_sub\Functional;

use Drupal\commerce_sub\Entity\Sub;
use Drupal\commerce_sub\Entity\SubVariation;

/**
 * Create, view, edit, delete, and change subs.
 *
 * @group commerce
 */
class SubAdminTest extends SubBrowserTestBase {

  /**
   * Tests creating a sub.
   */
  public function testCreateSub() {
    $this->drupalGet('admin/commerce/subs');
    $this->getSession()->getPage()->clickLink('Add sub');
    // Check the integrity of the add form.
    $this->assertSession()->fieldExists('title[0][value]');
    $this->assertSession()->fieldExists('variations[form][inline_entity_form][sku][0][value]');
    $this->assertSession()->fieldExists('variations[form][inline_entity_form][price][0][number]');
    $this->assertSession()->fieldExists('variations[form][inline_entity_form][status][value]');
    $this->assertSession()->buttonExists('Create variation');

    $store_ids = array_map(function ($store) {
      return $store->id();
    }, $this->stores);
    $title = $this->randomMachineName();
    $edit = [
      'title[0][value]' => $title,
    ];
    foreach ($store_ids as $store_id) {
      $edit['stores[target_id][value][' . $store_id . ']'] = $store_id;
    }
    $variation_sku = $this->randomMachineName();
    $variations_edit = [
      'variations[form][inline_entity_form][sku][0][value]' => $variation_sku,
      'variations[form][inline_entity_form][price][0][number]' => '9.99',
      'variations[form][inline_entity_form][status][value]' => 1,
    ];
    $this->submitForm($variations_edit, t('Create variation'));
    $this->submitForm($edit, t('Save and publish'));

    $result = \Drupal::entityQuery('commerce_sub')
      ->condition("title", $edit['title[0][value]'])
      ->range(0, 1)
      ->execute();
    $sub_id = reset($result);
    $sub = Sub::load($sub_id);

    $this->assertNotNull($sub, 'The new sub has been created.');
    $this->assertSession()->pageTextContains(t('The sub @title has been successfully saved', ['@title' => $title]));
    $this->assertSession()->pageTextContains($title);
    $this->assertFieldValues($sub->getStores(), $this->stores, 'Created sub has the correct associated stores.');
    $this->assertFieldValues($sub->getStoreIds(), $store_ids, 'Created sub has the correct associated store ids.');
    $this->drupalGet($sub->toUrl('canonical'));
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->pageTextContains($sub->getTitle());

    $variation = \Drupal::entityQuery('commerce_sub_variation')
      ->condition('sku', $variation_sku)
      ->range(0, 1)
      ->execute();

    $variation = SubVariation::load(current($variation));
    $this->assertNotNull($variation, 'The new sub variation has been created.');
  }

  /**
   * Tests editing a sub.
   */
  public function testEditSub() {
    $variation = $this->createEntity('commerce_sub_variation', [
      'type' => 'default',
      'sku' => strtolower($this->randomMachineName()),
    ]);
    $sub = $this->createEntity('commerce_sub', [
      'type' => 'default',
      'variations' => [$variation],
    ]);

    // Check the integrity of the edit form.
    $this->drupalGet($sub->toUrl('edit-form'));
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->fieldExists('title[0][value]');
    $this->assertSession()->buttonExists('edit-variations-entities-0-actions-ief-entity-edit');
    $this->submitForm([], t('Edit'));
    $this->assertSession()->fieldExists('variations[form][inline_entity_form][entities][0][form][sku][0][value]');
    $this->assertSession()->fieldExists('variations[form][inline_entity_form][entities][0][form][price][0][number]');
    $this->assertSession()->fieldExists('variations[form][inline_entity_form][entities][0][form][status][value]');
    $this->assertSession()->buttonExists('Update variation');

    $title = $this->randomMachineName();
    $store_ids = array_map(function ($store) {
      return $store->id();
    }, $this->stores);
    $edit = [
      'title[0][value]' => $title,
    ];
    foreach ($store_ids as $store_id) {
      $edit['stores[target_id][value][' . $store_id . ']'] = $store_id;
    }
    $new_sku = strtolower($this->randomMachineName());
    $new_price_amount = '1.11';
    $variations_edit = [
      'variations[form][inline_entity_form][entities][0][form][sku][0][value]' => $new_sku,
      'variations[form][inline_entity_form][entities][0][form][price][0][number]' => $new_price_amount,
      'variations[form][inline_entity_form][entities][0][form][status][value]' => 1,
    ];
    $this->submitForm($variations_edit, 'Update variation');
    $this->submitForm($edit, 'Save and keep published');

    \Drupal::service('entity_type.manager')->getStorage('commerce_sub_variation')->resetCache([$variation->id()]);
    $variation = SubVariation::load($variation->id());
    $this->assertEquals($variation->getSku(), $new_sku, 'The variation sku successfully updated.');
    $this->assertEquals($variation->get('price')->number, $new_price_amount, 'The variation price successfully updated.');
    \Drupal::service('entity_type.manager')->getStorage('commerce_sub')->resetCache([$sub->id()]);
    $sub = Sub::load($sub->id());
    $this->assertEquals($sub->getTitle(), $title, 'The sub title successfully updated.');
    $this->assertFieldValues($sub->getStores(), $this->stores, 'Updated sub has the correct associated stores.');
    $this->assertFieldValues($sub->getStoreIds(), $store_ids, 'Updated sub has the correct associated store ids.');
  }

  /**
   * Tests deleting a sub.
   */
  public function testDeleteSub() {
    $sub = $this->createEntity('commerce_sub', [
      'title' => $this->randomMachineName(),
      'type' => 'default',
    ]);
    $this->drupalGet($sub->toUrl('delete-form'));
    $this->assertSession()->pageTextContains(t("Are you sure you want to delete the sub @sub?", ['@sub' => $sub->getTitle()]));
    $this->assertSession()->pageTextContains(t('This action cannot be undone.'));
    $this->submitForm([], 'Delete');

    \Drupal::service('entity_type.manager')->getStorage('commerce_sub')->resetCache();
    $sub_exists = (bool) Sub::load($sub->id());
    $this->assertFalse($sub_exists, 'The new sub has been deleted from the database.');
  }

  /**
   * Tests that anonymous users cannot see the admin/commerce/subs page.
   */
  public function testAdminSubs() {
    $this->drupalGet('admin/commerce/subs');
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->pageTextNotContains('You are not authorized to access this page.');
    $this->assertTrue($this->getSession()->getPage()->hasLink('Add sub'));

    // Logout and check that anonymous users cannot see the subs page
    // and receive a 403 error code.
    $this->drupalLogout();
    $this->drupalGet('admin/commerce/subs');
    $this->assertSession()->statusCodeEquals(403);
    $this->assertSession()->pageTextContains('You are not authorized to access this page.');
    $this->assertTrue(!$this->getSession()->getPage()->hasLink('Add sub'));
  }

}
