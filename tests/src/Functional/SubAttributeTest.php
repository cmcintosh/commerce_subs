<?php

namespace Drupal\Tests\commerce_sub\Functional;

use Drupal\commerce_sub\Entity\SubAttribute;

/**
 * Create, edit, delete, and change sub attributes.
 *
 * @group commerce
 */
class SubAttributeTest extends SubBrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected function getAdministratorPermissions() {
    return array_merge([
      'administer commerce_sub_attribute',
    ], parent::getAdministratorPermissions());
  }

  /**
   * Tests creation of a sub attribute.
   */
  public function testSubAttributeCreation() {
    $this->drupalGet('admin/commerce/sub-attributes');
    $this->getSession()->getPage()->clickLink('Add sub attribute');
    $this->submitForm([
      'label' => 'Size',
      'elementType' => 'commerce_sub_rendered_attribute',
      // Setting the 'id' can fail if focus switches to another field.
      // This is a bug in the machine name JS that can be reproduced manually.
      'id' => 'size',
    ], 'Save');
    $this->assertSession()->pageTextContains('Created the Size sub attribute.');
    $this->assertSession()->addressMatches('/\/admin\/commerce\/sub-attributes\/manage\/size$/');

    $attribute = SubAttribute::load('size');
    $this->assertEquals($attribute->label(), 'Size');
    $this->assertEquals($attribute->getElementType(), 'commerce_sub_rendered_attribute');
  }

  /**
   * Tests editing a sub attribute.
   */
  public function testSubAttributeEditing() {
    $this->createEntity('commerce_sub_attribute', [
      'id' => 'color',
      'label' => 'Color',
    ]);
    $this->drupalGet('admin/commerce/sub-attributes/manage/color');
    $this->submitForm([
      'label' => 'Colour',
      'elementType' => 'radios',
      'values[0][entity][name][0][value]' => 'Red',
    ], 'Save');
    $this->assertSession()->pageTextContains('Updated the Colour sub attribute.');
    $this->assertSession()->addressMatches('/\/admin\/commerce\/sub-attributes$/');

    $attribute = SubAttribute::load('color');
    $this->assertEquals($attribute->label(), 'Colour');
    $this->assertEquals($attribute->getElementType(), 'radios');
  }

  /**
   * Tests deletion of a sub attribute.
   */
  public function testSubAttributeDeletion() {
    $this->createEntity('commerce_sub_attribute', [
      'id' => 'size',
      'label' => 'Size',
    ]);
    $this->drupalGet('admin/commerce/sub-attributes/manage/size/delete');
    $this->assertSession()->pageTextContains('Are you sure you want to delete the sub attribute Size?');
    $this->assertSession()->pageTextContains('This action cannot be undone.');
    $this->submitForm([], 'Delete');

    $this->assertNull(SubAttribute::load('size'));
  }

  /**
   * Tests assigning an attribute to a sub variation type.
   */
  public function testSubVariationTypes() {
    $this->createEntity('commerce_sub_attribute', [
      'id' => 'color',
      'label' => 'Color',
    ]);

    $this->drupalGet('admin/commerce/sub-attributes/manage/color');
    $edit = [
      'variation_types[default]' => 'default',
      'values[0][entity][name][0][value]' => 'Red',
    ];
    $this->submitForm($edit, t('Save'));
    $this->drupalGet('admin/commerce/config/sub-variation-types/default/edit/fields');
    $this->assertSession()->pageTextContains('attribute_color', 'The color attribute field has been created');

    $this->drupalGet('admin/commerce/sub-attributes/manage/color');
    $edit = [
      'variation_types[default]' => FALSE,
    ];
    $this->submitForm($edit, t('Save'));
    $this->drupalGet('admin/commerce/config/sub-variation-types/default/edit/fields');
    $this->assertSession()->pageTextNotContains('attribute_color', 'The color attribute field has been deleted');
  }

}
