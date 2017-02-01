<?php

namespace Drupal\Tests\commerce_sub\Kernel;

use Drupal\commerce_sub\Entity\SubAttribute;
use Drupal\commerce_sub\Entity\SubVariationType;
use Drupal\Tests\commerce\Kernel\CommerceKernelTestBase;

/**
 * Tests the attribute field manager.
 *
 * @coversDefaultClass \Drupal\commerce_sub\SubAttributeFieldManager
 *
 * @group commerce
 */
class SubAttributeFieldManagerTest extends CommerceKernelTestBase {

  /**
   * The attribute field manager.
   *
   * @var \Drupal\commerce_sub\SubAttributeFieldManagerInterface
   */
  protected $attributeFieldManager;

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
    $this->installEntitySchema('commerce_sub_variation');
    $this->installEntitySchema('commerce_sub_variation_type');
    $this->installEntitySchema('commerce_sub');
    $this->installEntitySchema('commerce_sub_type');

    $this->attributeFieldManager = $this->container->get('commerce_sub.attribute_field_manager');

    $first_variation_type = SubVariationType::create([
      'id' => 'shirt',
      'label' => 'Shirt',
    ]);
    $first_variation_type->save();
    $second_variation_type = SubVariationType::create([
      'id' => 'mug',
      'label' => 'Mug',
    ]);
    $second_variation_type->save();
  }

  /**
   * @covers ::getFieldDefinitions
   * @covers ::getFieldMap
   * @covers ::clearCaches
   * @covers ::createField
   * @covers ::canDeleteField
   * @covers ::deleteField
   */
  public function testManager() {
    $color_attribute = SubAttribute::create([
      'id' => 'color',
      'label' => 'Color',
    ]);
    $color_attribute->save();
    $size_attribute = SubAttribute::create([
      'id' => 'size',
      'label' => 'Size',
    ]);
    $size_attribute->save();

    $this->assertEquals([], $this->attributeFieldManager->getFieldMap('shirt'));
    $this->attributeFieldManager->createField($color_attribute, 'shirt');
    $this->attributeFieldManager->createField($size_attribute, 'shirt');
    $field_map = $this->attributeFieldManager->getFieldMap('shirt');
    $expected_field_map = [
      ['attribute_id' => 'color', 'field_name' => 'attribute_color'],
      ['attribute_id' => 'size', 'field_name' => 'attribute_size'],
    ];
    $this->assertEquals($expected_field_map, $field_map);

    $this->attributeFieldManager->createField($color_attribute, 'mug');
    $this->attributeFieldManager->createField($size_attribute, 'mug');
    $this->attributeFieldManager->deleteField($size_attribute, 'mug');
    $field_map = $this->attributeFieldManager->getFieldMap('mug');
    $expected_field_map = [
      ['attribute_id' => 'color', 'field_name' => 'attribute_color'],
    ];
    $this->assertEquals($expected_field_map, $field_map);

    $field_map = $this->attributeFieldManager->getFieldMap();
    $expected_field_map = [
      'shirt' => [
        ['attribute_id' => 'color', 'field_name' => 'attribute_color'],
        ['attribute_id' => 'size', 'field_name' => 'attribute_size'],
      ],
      'mug' => [
        ['attribute_id' => 'color', 'field_name' => 'attribute_color'],
      ],
    ];
    $this->assertEquals($expected_field_map, $field_map);
  }

}
