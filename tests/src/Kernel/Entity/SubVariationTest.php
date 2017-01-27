<?php

namespace Drupal\Tests\commerce_sub\Kernel\Entity;

use Drupal\commerce_price\Price;
use Drupal\commerce_sub\Entity\SubVariation;
use Drupal\commerce_sub\Entity\Sub;
use Drupal\Tests\commerce\Kernel\CommerceKernelTestBase;

/**
 * Tests the Sub variation entity.
 *
 * @coversDefaultClass \Drupal\commerce_sub\Entity\SubVariation
 *
 * @group commerce
 */
class SubVariationTest extends CommerceKernelTestBase {

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
   * @covers ::getOrderItemTypeId
   * @covers ::getOrderItemTitle
   * @covers ::getSub
   * @covers ::getSubId
   * @covers ::getSku
   * @covers ::setSku
   * @covers ::getTitle
   * @covers ::setTitle
   * @covers ::getPrice
   * @covers ::setPrice
   * @covers ::isActive
   * @covers ::setActive
   * @covers ::getCreatedTime
   * @covers ::setCreatedTime
   * @covers ::getOwner
   * @covers ::setOwner
   * @covers ::getOwnerId
   * @covers ::setOwnerId
   * @covers ::getStores
   */
  public function testSubVariation() {
    /** @var \Drupal\commerce_sub\Entity\SubVariationInterface $variation */
    $variation = SubVariation::create([
      'type' => 'default',
    ]);
    $variation->save();

    /** @var \Drupal\commerce_sub\Entity\SubInterface $sub */
    $sub = Sub::create([
      'type' => 'default',
      'title' => 'My Sub Title',
      'variations' => [$variation],
    ]);
    $sub->save();
    $sub = $this->reloadEntity($sub);
    $variation = $this->reloadEntity($variation);

    $this->assertEquals('default', $variation->getOrderItemTypeId());
    $this->assertEquals('My Sub Title', $variation->getOrderItemTitle());

    $this->assertEquals($sub, $variation->getSub());
    $this->assertEquals($sub->id(), $variation->getSubId());

    $variation->setSku('1001');
    $this->assertEquals('1001', $variation->getSku());

    $variation->setTitle('My title');
    $this->assertEquals('My title', $variation->getTitle());

    $price = new Price('9.99', 'USD');
    $variation->setPrice($price);
    $this->assertEquals($price, $variation->getPrice());

    $variation->setActive(TRUE);
    $this->assertEquals(TRUE, $variation->isActive());

    $variation->setCreatedTime(635879700);
    $this->assertEquals(635879700, $variation->getCreatedTime());

    $variation->setOwner($this->user);
    $this->assertEquals($this->user, $variation->getOwner());
    $this->assertEquals($this->user->id(), $variation->getOwnerId());
    $variation->setOwnerId(0);
    $this->assertEquals(NULL, $variation->getOwner());
    $variation->setOwnerId($this->user->id());
    $this->assertEquals($this->user, $variation->getOwner());
    $this->assertEquals($this->user->id(), $variation->getOwnerId());

    $this->assertEquals($sub->getStores(), $variation->getStores());
  }

}
