<?php

namespace Drupal\Tests\commerce_sub\Kernel;

use Drupal\commerce_sub\Entity\SubAttribute;
use Drupal\commerce_sub\Entity\SubAttributeValue;
use Drupal\Tests\commerce\Kernel\CommerceKernelTestBase;

/**
 * Tests the sub attribute value storage.
 *
 * @group commerce
 */
class SubAttributeValueStorageTest extends CommerceKernelTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = [
    'path',
    'commerce_sub',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->installEntitySchema('commerce_sub_attribute');
    $this->installEntitySchema('commerce_sub_attribute_value');
  }

  /**
   * Tests loadByAttribute()
   */
  public function testLoadByAttribute() {
    $color_attribute = SubAttribute::create([
      'id' => 'color',
      'label' => 'Color',
    ]);
    $color_attribute->save();

    SubAttributeValue::create([
      'attribute' => 'color',
      'name' => 'Black',
      'weight' => 3,
    ])->save();
    SubAttributeValue::create([
      'attribute' => 'color',
      'name' => 'Yellow',
      'weight' => 2,
    ])->save();
    SubAttributeValue::create([
      'attribute' => 'color',
      'name' => 'Magenta',
      'weight' => 1,
    ])->save();
    SubAttributeValue::create([
      'attribute' => 'color',
      'name' => 'Cyan',
      'weight' => 0,
    ])->save();

    /** @var \Drupal\commerce_sub\SubAttributeValueStorageInterface $attribute_value_storage */
    $attribute_value_storage = \Drupal::service('entity_type.manager')->getStorage('commerce_sub_attribute_value');
    /** @var \Drupal\commerce_sub\Entity\SubAttributeValueInterface[] $attribute_values */
    $attribute_values = $attribute_value_storage->loadByAttribute('color');

    $value = array_shift($attribute_values);
    $this->assertEquals('Cyan', $value->getName());
    $value = array_shift($attribute_values);
    $this->assertEquals('Magenta', $value->getName());
    $value = array_shift($attribute_values);
    $this->assertEquals('Yellow', $value->getName());
    $value = array_shift($attribute_values);
    $this->assertEquals('Black', $value->getName());
  }

}
