<?php

namespace Drupal\commerce_sub;

use Drupal\commerce_sub\Entity\SubType;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;

/**
 * Defines the list builder for subs.
 */
class SubListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['title'] = t('Title');
    $header['type'] = t('Type');
    $header['status'] = t('Status');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /** @var \Drupal\commerce_sub\Entity\SubInterface $entity */
    $sub_type = SubType::load($entity->bundle());

    $row['title']['data'] = [
      '#type' => 'link',
      '#title' => $entity->label(),
    ] + $entity->toUrl()->toRenderArray();
    $row['type'] = $sub_type->label();
    $row['status'] = $entity->isPublished() ? $this->t('Published') : $this->t('Unpublished');

    return $row + parent::buildRow($entity);
  }

}
