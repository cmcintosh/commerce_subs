<?php

namespace Drupal\Tests\commerce_sub\Kernel\Entity;

use Drupal\commerce_sub\Entity\SubVariation;
use Drupal\commerce_sub\Entity\Sub;
use Drupal\Tests\commerce\Kernel\CommerceKernelTestBase;

/**
 * Tests the Sub entity.
 *
 * @coversDefaultClass \Drupal\commerce_sub\Entity\Sub
 *
 * @group commerce
 */
class SubTest extends CommerceKernelTestBase {

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
   * A sample user.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $user;

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

    $user = $this->createUser();
    $this->user = $this->reloadEntity($user);
  }

  /**
   * @covers ::getTitle
   * @covers ::setTitle
   * @covers ::isPublished
   * @covers ::setPublished
   * @covers ::getCreatedTime
   * @covers ::setCreatedTime
   * @covers ::getStores
   * @covers ::setStores
   * @covers ::getStoreIds
   * @covers ::setStoreIds
   * @covers ::getOwner
   * @covers ::setOwner
   * @covers ::getOwnerId
   * @covers ::setOwnerId
   */
  public function testSub() {
    $sub = Sub::create([
      'type' => 'default',
    ]);
    $sub->save();

    $sub->setTitle('My title');
    $this->assertEquals('My title', $sub->getTitle());

    $this->assertEquals(TRUE, $sub->isPublished());
    $sub->setPublished(FALSE);
    $this->assertEquals(FALSE, $sub->isPublished());

    $sub->setCreatedTime(635879700);
    $this->assertEquals(635879700, $sub->getCreatedTime());

    $sub->setStores([$this->store]);
    $this->assertEquals([$this->store], $sub->getStores());
    $this->assertEquals([$this->store->id()], $sub->getStoreIds());
    $sub->setStores([]);
    $this->assertEquals([], $sub->getStores());
    $sub->setStoreIds([$this->store->id()]);
    $this->assertEquals([$this->store], $sub->getStores());
    $this->assertEquals([$this->store->id()], $sub->getStoreIds());

    $sub->setOwner($this->user);
    $this->assertEquals($this->user, $sub->getOwner());
    $this->assertEquals($this->user->id(), $sub->getOwnerId());
    $sub->setOwnerId(0);
    $this->assertEquals(NULL, $sub->getOwner());
    $sub->setOwnerId($this->user->id());
    $this->assertEquals($this->user, $sub->getOwner());
    $this->assertEquals($this->user->id(), $sub->getOwnerId());
  }

  /**
   * @covers ::getVariationIds
   * @covers ::getVariations
   * @covers ::setVariations
   * @covers ::hasVariations
   * @covers ::addVariation
   * @covers ::removeVariation
   * @covers ::hasVariation
   * @covers ::getDefaultVariation
   */
  public function testVariationMethods() {
    $variation1 = SubVariation::create([
      'type' => 'default',
      'sku' => strtolower($this->randomMachineName()),
      'title' => $this->randomString(),
      'status' => 0,
    ]);
    $variation1->save();

    $variation2 = SubVariation::create([
      'type' => 'default',
      'sku' => strtolower($this->randomMachineName()),
      'title' => $this->randomString(),
      'status' => 1,
    ]);
    $variation2->save();

    $sub = Sub::create([
      'type' => 'default',
    ]);
    $sub->save();

    // An initially saved variation won't be the same as the loaded one.
    $variation1 = SubVariation::load($variation1->id());
    $variation2 = SubVariation::load($variation2->id());

    $variations = [$variation1, $variation2];
    $this->assertFalse($sub->hasVariations());
    $sub->setVariations($variations);
    $this->assertTrue($sub->hasVariations());
    $variations_match = $variations == $sub->getVariations();
    $this->assertTrue($variations_match);
    $variation_ids = [$variation1->id(), $variation2->id()];
    $variation_ids_match = $variation_ids == $sub->getVariationIds();
    $this->assertTrue($variation_ids_match);

    $this->assertTrue($sub->hasVariation($variation1));
    $sub->removeVariation($variation1);
    $this->assertFalse($sub->hasVariation($variation1));
    $sub->addVariation($variation1);
    $this->assertTrue($sub->hasVariation($variation1));

    $this->assertEquals($sub->getDefaultVariation(), $variation2);
    $this->assertNotEquals($sub->getDefaultVariation(), $variation1);
  }

  /**
   * Tests variation's canonical URL.
   */
  public function testCanonicalVariationLink() {
    $variation1 = SubVariation::create([
      'type' => 'default',
      'sku' => strtolower($this->randomMachineName()),
      'title' => $this->randomString(),
      'status' => 0,
    ]);
    $variation1->save();
    $sub = Sub::create([
      'type' => 'default',
      'title' => 'My sub',
      'variations' => [$variation1],
    ]);
    $sub->save();

    $sub_url = $sub->toUrl()->toString();
    $variation_url = $variation1->toUrl()->toString();
    $this->assertEquals($sub_url . '?v=' . $variation1->id(), $variation_url);
  }

}
