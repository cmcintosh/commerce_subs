<?php

namespace Drupal\Tests\commerce_sub\Functional;

use Drupal\commerce_sub\Entity\SubType;

/**
 * Ensure the sub type works correctly.
 *
 * @group commerce
 */
class SubTypeTest extends SubBrowserTestBase {

  /**
   * Tests whether the default sub type was created.
   */
  public function testDefaultSubType() {
    $sub_type = SubType::load('default');
    $this->assertTrue(!empty($sub_type), 'The default sub type is available.');

    $this->drupalGet('admin/commerce/config/sub-types');
    $rows = $this->getSession()->getPage()->find('css', 'table tbody tr');
    $this->assertEquals(count($rows), 1, '1 sub type is correctly listed.');
  }

  /**
   * Tests creating a sub type programmatically and via a form.
   */
  public function testSubTypeCreation() {
    $values = [
      'id' => strtolower($this->randomMachineName(8)),
      'label' => $this->randomMachineName(),
      'description' => 'My random sub type',
      'variationType' => 'default',
    ];
    $this->createEntity('commerce_sub_type', $values);
    $sub_type = SubType::load($values['id']);
    $this->assertEquals($sub_type->label(), $values['label'], 'The new sub type has the correct label.');
    $this->assertEquals($sub_type->getDescription(), $values['description'], 'The new sub type has the correct label.');
    $this->assertEquals($sub_type->getVariationTypeId(), $values['variationType'], 'The new sub type has the correct associated variation type.');

    $this->drupalGet('sub/add/' . $sub_type->id());
    $this->assertSession()->statusCodeEquals(200);

    $user = $this->drupalCreateUser(['administer commerce_sub_type']);
    $this->drupalLogin($user);

    $this->drupalGet('admin/commerce/config/sub-types/add');
    $edit = [
      'id' => strtolower($this->randomMachineName(8)),
      'label' => $this->randomMachineName(),
      'description' => 'My even more random sub type',
      'variationType' => 'default',
    ];
    $this->submitForm($edit, t('Save'));
    $sub_type = SubType::load($edit['id']);
    $this->assertTrue(!empty($sub_type), 'The new sub type has been created.');
    $this->assertEquals($sub_type->label(), $edit['label'], 'The new sub type has the correct label.');
    $this->assertEquals($sub_type->getDescription(), $edit['description'], 'The new sub type has the correct label.');
    $this->assertEquals($sub_type->getVariationTypeId(), $edit['variationType'], 'The new sub type has the correct associated variation type.');
  }

  /**
   * Tests editing a sub type using the UI.
   */
  public function testSubTypeEditing() {
    $this->drupalGet('admin/commerce/config/sub-types/default/edit');
    $edit = [
      'label' => 'Default2',
      'description' => 'New description.',
    ];
    $this->submitForm($edit, t('Save'));
    $sub_type = SubType::load('default');
    $this->assertEquals($sub_type->label(), $edit['label'], 'The label of the sub type has been changed.');
    $this->assertEquals($sub_type->getDescription(), $edit['description'], 'The new sub type has the correct label.');
  }

  /**
   * Tests deleting a sub type via a form.
   */
  public function testSubTypeDeletion() {
    $variation_type = $this->createEntity('commerce_sub_variation_type', [
      'id' => 'foo',
      'label' => 'foo',
    ]);
    $sub_type = $this->createEntity('commerce_sub_type', [
      'id' => 'foo',
      'label' => 'foo',
      'variationType' => $variation_type->id(),
    ]);
    commerce_sub_add_stores_field($sub_type);
    commerce_sub_add_variations_field($sub_type);

    $sub = $this->createEntity('commerce_sub', [
      'type' => $sub_type->id(),
      'title' => $this->randomMachineName(),
    ]);

    // @todo Make sure $sub_type->delete() also does nothing if there's
    // a sub of that type. Right now the check is done on the form level.
    $this->drupalGet('admin/commerce/config/sub-types/' . $sub_type->id() . '/delete');
    $this->assertSession()->pageTextContains(
      t('@type is used by 1 sub on your site. You can not remove this sub type until you have removed all of the @type subs.', ['@type' => $sub_type->label()]),
      'The sub type will not be deleted until all subs of that type are deleted.'
    );
    $this->assertSession()->pageTextNotContains(t('This action cannot be undone.'));

    $sub->delete();
    $this->drupalGet('admin/commerce/config/sub-types/' . $sub_type->id() . '/delete');
    $this->assertSession()->pageTextContains(
      t('Are you sure you want to delete the sub type @type?', ['@type' => $sub_type->label()]),
      'The sub type is available for deletion'
    );
    $this->assertSession()->pageTextContains(t('This action cannot be undone.'));
    $this->submitForm([], 'Delete');
    $exists = (bool) SubType::load($sub_type->id());
    $this->assertFalse($exists, 'The new sub type has been deleted from the database.');
  }

}
