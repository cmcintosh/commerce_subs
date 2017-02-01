<?php

namespace Drupal\Tests\commerce_sub\Functional;

use Drupal\commerce_sub\Entity\SubVariationType;

/**
 * Tests the sub variation title generation.
 *
 * @group commerce
 */
class SubVariationTitleGenerationTest extends SubBrowserTestBase {

  /**
   * The variation type to test against.
   *
   * @var \Drupal\commerce_sub\Entity\SubVariationTypeInterface
   */
  protected $variationType;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->variationType = $this->createEntity('commerce_sub_variation_type', [
      'id' => 'test_default',
      'label' => 'Test Default',
      'orderItemType' => 'default',
    ]);
  }

  /**
   * Test the variation type setting.
   */
  public function testTitleGenerationSetting() {
    /** @var \Drupal\Core\Field\BaseFieldDefinition[] $field_definitions */
    $this->assertFalse($this->variationType->shouldGenerateTitle());
    $field_definitions = \Drupal::service('entity_field.manager')->getFieldDefinitions('commerce_sub_variation', $this->variationType->id());
    $this->assertTrue($field_definitions['title']->isRequired());

    // Enable generation.
    $this->variationType->setGenerateTitle(TRUE);
    $this->variationType->save();
    /** @var \Drupal\commerce_sub\Entity\SubVariationTypeInterface $variation_type */
    $variation_type = SubVariationType::load($this->variationType->id());
    $this->assertTrue($variation_type->shouldGenerateTitle());

    /** @var \Drupal\Core\Field\BaseFieldDefinition[] $field_definitions */
    $field_definitions = \Drupal::service('entity_field.manager')->getFieldDefinitions('commerce_sub_variation', $this->variationType->id());
    $this->assertFalse($field_definitions['title']->isRequired());
  }

  /**
   * Test the title generation.
   */
  public function testTitleGeneration() {
    /** @var \Drupal\commerce_sub\Entity\SubVariationInterface $variation */
    $variation = $this->createEntity('commerce_sub_variation', [
      'type' => 'test_default',
      'sku' => strtolower($this->randomMachineName()),
    ]);
    /** @var \Drupal\commerce_sub\Entity\SubInterface $sub */
    $this->createEntity('commerce_sub', [
      'type' => 'default',
      'title' => 'My sub',
      'variations' => [$variation],
    ]);
    $this->assertTrue(empty($variation->getTitle()));

    $this->variationType->setGenerateTitle(TRUE);
    $this->variationType->save();

    /** @var \Drupal\commerce_sub\Entity\SubVariationInterface $variation */
    $variation = $this->createEntity('commerce_sub_variation', [
      'type' => 'test_default',
      'sku' => strtolower($this->randomMachineName()),
    ]);
    /** @var \Drupal\commerce_sub\Entity\SubInterface $sub */
    $sub = $this->createEntity('commerce_sub', [
      'type' => 'default',
      'title' => 'My second sub',
      'variations' => [$variation],
    ]);
    $this->assertEquals($variation->getTitle(), $sub->getTitle());

    // @todo Create attributes, then retest title generation.
  }

}
