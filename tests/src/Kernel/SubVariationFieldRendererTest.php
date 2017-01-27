<?php

namespace Drupal\Tests\commerce_sub\Kernel;

use Drupal\commerce_sub\Entity\Sub;
use Drupal\commerce_sub\Entity\SubAttribute;
use Drupal\commerce_sub\Entity\SubVariation;
use Drupal\commerce_sub\Entity\SubVariationType;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\Tests\commerce\Kernel\CommerceKernelTestBase;

/**
 * Tests the sub variation field renderer.
 *
 * @coversDefaultClass \Drupal\commerce_sub\SubVariationFieldRenderer
 *
 * @group commerce
 */
class SubVariationFieldRendererTest extends CommerceKernelTestBase {

  /**
   * The variation field injection.
   *
   * @var \Drupal\commerce_sub\SubVariationFieldRendererInterface
   */
  protected $variationFieldRenderer;

  /**
   * The first variation type.
   *
   * @var \Drupal\commerce_sub\Entity\SubVariationType
   */
  protected $firstVariationType;

  /**
   * The second variation type.
   *
   * @var \Drupal\commerce_sub\Entity\SubVariationType
   */
  protected $secondVariationType;

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

    $this->installEntitySchema('commerce_sub_variation');
    $this->installEntitySchema('commerce_sub_variation_type');
    $this->installEntitySchema('commerce_sub');
    $this->installEntitySchema('commerce_sub_type');
    $this->installConfig(['commerce_sub']);

    $this->variationFieldRenderer = $this->container->get('commerce_sub.variation_field_renderer');

    $this->firstVariationType = SubVariationType::create([
      'id' => 'shirt',
      'label' => 'Shirt',
    ]);
    $this->firstVariationType->save();
    $this->secondVariationType = SubVariationType::create([
      'id' => 'mug',
      'label' => 'Mug',
    ]);
    $this->secondVariationType->save();

    $field_storage = FieldStorageConfig::create([
      'field_name' => 'render_field',
      'entity_type' => 'commerce_sub_variation',
      'type' => 'text',
      'cardinality' => 1,
    ]);
    $field_storage->save();

    $field = FieldConfig::create([
      'field_storage' => $field_storage,
      'bundle' => $this->secondVariationType->id(),
      'label' => 'Render field',
      'required' => TRUE,
      'translatable' => FALSE,
    ]);
    $field->save();

    $attribute = SubAttribute::create([
      'id' => 'color',
      'label' => 'Color',
    ]);
    $attribute->save();

    $this->container->get('commerce_sub.attribute_field_manager')
      ->createField($attribute, $this->secondVariationType->id());
  }

  /**
   * Tests the getFieldDefinitions method.
   *
   * @covers ::getFieldDefinitions
   */
  public function testGetFieldDefinitions() {
    $field_definitions = $this->variationFieldRenderer->getFieldDefinitions($this->firstVariationType->id());
    $field_names = array_keys($field_definitions);
    $this->assertEquals(['sku', 'title', 'price'], $field_names, 'The title, sku, price variation fields are renderable.');

    $field_definitions = $this->variationFieldRenderer->getFieldDefinitions($this->secondVariationType->id());
    $field_names = array_keys($field_definitions);
    $this->assertEquals(
      ['sku', 'title', 'price', 'render_field', 'attribute_color'],
      $field_names,
      'The title, sku, price, render_field, attribute_color variation fields are renderable.'
    );
  }

  /**
   * Tests renderFields.
   *
   * @covers ::renderFields
   * @covers ::renderField
   */
  public function testRenderFields() {
    $variation = SubVariation::create([
      'type' => $this->secondVariationType->id(),
      'sku' => strtolower($this->randomMachineName()),
      'title' => $this->randomString(),
      'status' => 1,
    ]);
    $variation->save();
    $sub = Sub::create([
      'type' => 'default',
      'variations' => [$variation],
    ]);
    $sub->save();

    $sub_display = commerce_get_entity_display('commerce_sub_variation', $variation->bundle(), 'view');
    $sub_display->setComponent('attribute_color', [
      'label' => 'above',
      'type' => 'entity_reference_label',
    ]);
    $sub_display->save();

    $rendered_fields = $this->variationFieldRenderer->renderFields($variation);
    $this->assertFalse(isset($rendered_fields['status']), 'Variation status field was not rendered');
    $this->assertTrue(isset($rendered_fields['sku']), 'Variation SKU field was rendered');
    $this->assertTrue(isset($rendered_fields['attribute_color']), 'Variation atrribute color field was rendered');
    $this->assertEquals('sub--variation-field--variation_sku__' . $variation->getSubId(), $rendered_fields['sku']['#ajax_replace_class']);
    $this->assertEquals('sub--variation-field--variation_attribute_color__' . $variation->getSubId(), $rendered_fields['attribute_color']['#ajax_replace_class']);

    $sub_view_builder = $this->container->get('entity_type.manager')->getViewBuilder('commerce_sub');
    $sub_build = $sub_view_builder->view($sub);
    $this->render($sub_build);

    $this->assertEmpty($this->cssSelect('.sub--variation-field--variation_attribute_color__' . $variation->getSubId()));
    $this->assertNotEmpty($this->cssSelect('.sub--variation-field--variation_sku__' . $variation->getSubId()));
  }

  /**
   * Tests that viewing a sub without variations does not throw fatal error.
   *
   * @see commerce_sub_commerce_sub_view()
   */
  public function testRenderFieldsNoVariations() {
    $sub = Sub::create([
      'type' => 'default',
      'variations' => [],
    ]);
    $sub->save();

    // The test will fail if we get a fatal error.
    $sub_view_builder = $this->container->get('entity_type.manager')->getViewBuilder('commerce_sub');
    $sub_build = $sub_view_builder->view($sub);
    $this->render($sub_build);
  }

}
