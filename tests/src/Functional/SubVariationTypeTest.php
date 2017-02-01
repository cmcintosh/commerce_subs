<?php

namespace Drupal\Tests\commerce_sub\Functional;

use Drupal\commerce_sub\Entity\SubVariationType;

/**
 * Ensure the sub variation type works correctly.
 *
 * @group commerce
 */
class SubVariationTypeTest extends SubBrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->createEntity('commerce_sub_attribute', [
      'id' => 'color',
      'label' => 'Color',
    ]);
  }

  /**
   * Tests whether the default sub variation type was created.
   */
  public function testDefaultSubVariationType() {
    $variation_type = SubVariationType::load('default');
    $this->assertTrue(!empty($variation_type), 'The default sub variation type is available.');

    $this->drupalGet('admin/commerce/config/sub-variation-types');
    $rows = $this->getSession()->getPage()->find('css', 'table tbody tr');
    $this->assertEquals(count($rows), 1, '1 sub variation type is correctly listed.');
  }

  /**
   * Tests creating a sub variation type programmatically and via a form.
   */
  public function testSubVariationTypeCreation() {
    $values = [
      'id' => strtolower($this->randomMachineName(8)),
      'label' => $this->randomMachineName(),
      'orderItemType' => 'default',
    ];
    $this->createEntity('commerce_sub_variation_type', $values);
    $variation_type = SubVariationType::load($values['id']);
    $this->assertEquals($variation_type->label(), $values['label'], 'The new sub variation type has the correct label.');
    $this->assertEquals($variation_type->getOrderItemTypeId(), $values['orderItemType'], 'The new sub variation type has the correct order item type.');

    $this->drupalGet('admin/commerce/config/sub-variation-types/add');
    $edit = [
      'id' => strtolower($this->randomMachineName(8)),
      'label' => $this->randomMachineName(),
      'orderItemType' => 'default',
    ];
    $this->submitForm($edit, t('Save'));
    $variation_type = SubVariationType::load($edit['id']);
    $this->assertTrue(!empty($variation_type), 'The new sub variation type has been created.');
    $this->assertEquals($variation_type->label(), $edit['label'], 'The new sub variation type has the correct label.');
    $this->assertEquals($variation_type->getOrderItemTypeId(), $edit['orderItemType'], 'The new sub variation type has the correct order item type.');
  }

  /**
   * Tests editing a sub variation type using the UI.
   */
  public function testSubVariationTypeEditing() {
    $this->drupalGet('admin/commerce/config/sub-variation-types/default/edit');
    $edit = [
      'label' => 'Default2',
    ];
    $this->submitForm($edit, t('Save'));
    $variation_type = SubVariationType::load('default');
    $this->assertEquals($variation_type->label(), 'Default2', 'The label of the sub variation type has been changed.');
  }

  /**
   * Tests deleting a sub variation type via a form.
   */
  public function testSubVariationTypeDeletion() {
    $variation_type = $this->createEntity('commerce_sub_variation_type', [
      'id' => 'foo',
      'label' => 'foo',
    ]);
    $variation = $this->createEntity('commerce_sub_variation', [
      'type' => $variation_type->id(),
      'sku' => $this->randomMachineName(),
      'title' => $this->randomMachineName(),
    ]);

    // @todo Make sure $variation_type->delete() also does nothing if there's
    // a variation of that type. Right now the check is done on the form level.
    $this->drupalGet('admin/commerce/config/sub-variation-types/' . $variation_type->id() . '/delete');
    $this->assertSession()->pageTextContains(
      t('@type is used by 1 sub variation on your site. You can not remove this sub variation type until you have removed all of the @type sub variations.', ['@type' => $variation_type->label()]),
      'The sub variation type will not be deleted until all variations of that type are deleted.'
    );
    $this->assertSession()->pageTextNotContains(t('This action cannot be undone.'), 'The sub variation type deletion confirmation form is not available');

    $variation->delete();
    $this->drupalGet('admin/commerce/config/sub-variation-types/' . $variation_type->id() . '/delete');
    $this->assertSession()->pageTextContains(
      t('Are you sure you want to delete the sub variation type @type?', ['@type' => $variation_type->label()]),
      'The sub variation type is available for deletion'
    );
    $this->assertSession()->pageTextContains(t('This action cannot be undone.'));
    $this->getSession()->getPage()->pressButton('Delete');
    $exists = (bool) SubVariationType::load($variation_type->id());
    $this->assertFalse($exists, 'The new sub variation type has been deleted from the database.');
  }

  /**
   * Tests the attributes element on the sub variation type form.
   */
  public function testSubVariationTypeAttributes() {
    $this->drupalGet('admin/commerce/config/sub-variation-types/default/edit');
    $edit = [
      'label' => 'Default',
      'orderItemType' => 'default',
      'attributes[color]' => 'color',
    ];
    $this->submitForm($edit, t('Save'));
    $this->drupalGet('admin/commerce/config/sub-variation-types/default/edit/fields');
    $this->assertSession()->pageTextContains('attribute_color', 'The color attribute field has been created');

    $this->drupalGet('admin/commerce/config/sub-variation-types/default/edit');
    $edit = [
      'label' => 'Default',
      'orderItemType' => 'default',
      'attributes[color]' => FALSE,
    ];
    $this->submitForm($edit, t('Save'));
    $this->drupalGet('admin/commerce/config/sub-variation-types/default/edit/fields');
    $this->assertSession()->pageTextNotContains('attribute_color', 'The color attribute field has been deleted');
  }

}
