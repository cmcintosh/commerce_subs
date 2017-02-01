<?php

namespace Drupal\Tests\commerce_sub\Kernel;

use Drupal\commerce_sub\Entity\Sub;
use Drupal\commerce_sub\Entity\SubVariation;
use Drupal\Tests\commerce\Kernel\CommerceKernelTestBase;

/**
 * Tests the sub variation storage.
 *
 * @group commerce
 */
class SubVariationStorageTest extends CommerceKernelTestBase {

  /**
   * The sub variation storage.
   *
   * @var \Drupal\commerce_sub\SubVariationStorageInterface
   */
  protected $variationStorage;

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

    $this->variationStorage = $this->container->get('entity_type.manager')->getStorage('commerce_sub_variation');
  }

  /**
   * Tests loadEnabled() function.
   */
  public function testLoadEnabled() {
    $variations = [];
    for ($i = 1; $i <= 3; $i++) {
      $variation = SubVariation::create([
        'type' => 'default',
        'sku' => strtolower($this->randomMachineName()),
        'title' => $this->randomString(),
        'status' => $i % 2,
      ]);
      $variation->save();
      $variations[] = $variation;
    }
    $variations = array_reverse($variations);
    $sub = Sub::create([
      'type' => 'default',
      'variations' => $variations,
    ]);
    $sub->save();

    $variationsFiltered = $this->variationStorage->loadEnabled($sub);
    $this->assertEquals(2, count($variationsFiltered), '2 out of 3 variations are enabled');
    $this->assertEquals(reset($variations)->getSku(), reset($variationsFiltered)->getSku(), 'The sort order of the variations remains the same');
  }

}
