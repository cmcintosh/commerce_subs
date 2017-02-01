<?php

namespace Drupal\Tests\commerce_sub\Kernel;

use Drupal\commerce_sub\Entity\Sub;
use Drupal\commerce_sub\Entity\SubAttribute;
use Drupal\commerce_sub\Entity\SubAttributeValue;
use Drupal\commerce_sub\Entity\SubVariation;
use Drupal\Core\Entity\Entity\EntityViewMode;
use Drupal\Tests\commerce\Kernel\CommerceKernelTestBase;

/**
 * Tests the "commerce_sub_attributes_overview" formatter.
 *
 * @group commerce
 */
class SubAttributesOverviewFormatterTest extends CommerceKernelTestBase {

  /**
   * @var \Drupal\commerce_sub\Entity\SubInterface
   */
  protected $sub;

  /**
   * @var \Drupal\Core\Entity\Display\EntityViewDisplayInterface
   */
  protected $subDefaultDisplay;

  /**
   * @var \Drupal\Core\Entity\Display\EntityViewDisplayInterface
   */
  protected $attributeDefaultDisplay;

  /**
   * @var \Drupal\Core\Entity\EntityViewBuilderInterface
   */
  protected $subViewBuilder;

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
    $this->installConfig(['commerce_sub']);

    $this->subDefaultDisplay = commerce_get_entity_display('commerce_sub', 'default', 'view');
    $this->attributeDefaultDisplay = commerce_get_entity_display('commerce_sub_attribute_value', 'color', 'view');
    $this->subViewBuilder = $this->container->get('entity_type.manager')->getViewBuilder('commerce_sub');

    $attribute = SubAttribute::create([
      'id' => 'color',
      'label' => 'Color',
    ]);
    $attribute->save();
    $this->container->get('commerce_sub.attribute_field_manager')->createField($attribute, 'default');

    EntityViewMode::create([
      'id' => 'commerce_sub.catalog',
      'label' => 'Catalog',
      'targetEntityType' => 'commerce_sub',
    ])->save();

    $this->container->get('router.builder')->rebuildIfNeeded();

    $attribute_values = [];
    $attribute_values['cyan'] = SubAttributeValue::create([
      'attribute' => $attribute->id(),
      'name' => 'Cyan',
    ]);
    $attribute_values['cyan']->save();
    $attribute_values['yellow'] = SubAttributeValue::create([
      'attribute' => $attribute->id(),
      'name' => 'Yellow',
    ]);
    $attribute_values['yellow']->save();

    $this->attributeDefaultDisplay->setComponent('name', [
      'label' => 'above',
    ]);
    $this->attributeDefaultDisplay->save();

    $variation1 = SubVariation::create([
      'type' => 'default',
      'sku' => strtolower($this->randomMachineName()),
      'price' => [
        'number' => 999,
        'currency_code' => 'USD',
      ],
      'attribute_color' => $attribute_values['cyan'],
    ]);
    $variation2 = SubVariation::create([
      'type' => 'default',
      'sku' => strtolower($this->randomMachineName()),
      'price' => [
        'number' => 999,
        'currency_code' => 'USD',
      ],
      'attribute_color' => $attribute_values['yellow'],
    ]);
    $variation3 = SubVariation::create([
      'type' => 'default',
      'sku' => strtolower($this->randomMachineName()),
      'price' => [
        'number' => 999,
        'currency_code' => 'USD',
      ],
      'attribute_color' => $attribute_values['yellow'],
    ]);
    /** @var \Drupal\commerce_sub\Entity\SubInterface $sub */
    $this->sub = Sub::create([
      'type' => 'default',
      'title' => 'My sub',
      'variations' => [$variation1, $variation2, $variation3],
    ]);
    $this->sub->save();
  }

  /**
   * Test the formatters rendered display.
   */
  public function testFormatterDisplay() {
    $this->subDefaultDisplay->setComponent('variations', [
      'type' => 'commerce_sub_attributes_overview',
      'settings' => [
        'attributes' => ['color' => 'color'],
        'view_mode' => 'default',
      ],
    ]);
    $this->subDefaultDisplay->save();

    $build = $this->subViewBuilder->view($this->sub);
    $this->render($build);

    $this->assertFieldByXPath('//h3[text()=\'Color\']');
    $this->assertFieldByXPath('//ul/li[1]/a/div/div/div[text()=\'Name\']');
    $this->assertFieldByXPath('//ul/li[1]/a/div/div/div[text()=\'Cyan\']');
    $this->assertFieldByXPath('//ul/li[2]/a/div/div/div[text()=\'Name\']');
    $this->assertFieldByXPath('//ul/li[2]/a/div/div/div[text()=\'Yellow\']');

    // Ensure Yellow rendered once, even though two variations have it.
    $this->assertEquals(1, count($this->xpath('//ul/li[2]/a/div/div/div[text()=\'Yellow\']')));

    $this->attributeDefaultDisplay->setComponent('name', [
      'label' => 'hidden',
    ]);
    $this->attributeDefaultDisplay->save();
    $this->subViewBuilder->resetCache([$this->sub]);

    $build = $this->subViewBuilder->view($this->sub);
    $this->render($build);

    $this->assertFieldByXPath('//h3[text()=\'Color\']');
    $this->assertFieldByXPath('//ul/li[1]/a/div/div[text()=\'Cyan\']');
    $this->assertFieldByXPath('//ul/li[2]/a/div/div[text()=\'Yellow\']');

    EntityViewMode::create([
      'id' => 'commerce_sub_attribute_value.test_display',
      'label' => 'Test Display',
      'targetEntityType' => 'commerce_sub_attribute_value',
    ])->save();
    $test_attribute_display_mode = $this->attributeDefaultDisplay->createCopy('test_display');
    $test_attribute_display_mode->setStatus(TRUE);
    $test_attribute_display_mode->setComponent('name', [
      'label' => 'inline',
    ]);
    $test_attribute_display_mode->save();

    $this->subDefaultDisplay->setComponent('variations', [
      'type' => 'commerce_sub_attributes_overview',
      'settings' => [
        'attributes' => ['color' => 'color'],
        'view_mode' => 'test_display',
      ],
    ]);
    $this->subDefaultDisplay->save();

    $this->subViewBuilder->resetCache([$this->sub]);

    $build = $this->subViewBuilder->view($this->sub);
    $this->render($build);

    $this->assertFieldByXPath('//h3[text()=\'Color\']');
    $this->assertFieldByXPath('//ul/li[1]/a/div/div/div[text()=\'Name\']');
    $this->assertFieldByXPath('//ul/li[1]/a/div/div/div[text()=\'Cyan\']');
    $this->assertFieldByXPath('//ul/li[2]/a/div/div/div[text()=\'Name\']');
    $this->assertFieldByXPath('//ul/li[2]/a/div/div/div[text()=\'Yellow\']');
  }

}
