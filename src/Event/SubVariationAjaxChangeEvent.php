<?php

namespace Drupal\commerce_sub\Event;

use Drupal\commerce_sub\Entity\SubVariationInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Symfony\Component\EventDispatcher\Event;

/**
 * Defines the sub variation ajax change event.
 *
 * @see \Drupal\commerce_sub\Event\SubEvents
 */
class SubVariationAjaxChangeEvent extends Event {

  /**
   * The sub variation.
   *
   * @var \Drupal\commerce_sub\Entity\SubVariationInterface
   */
  protected $subVariation;

  /**
   * The ajax response.
   *
   * @var \Drupal\Core\Ajax\AjaxResponse
   */
  protected $response;

  /**
   * The view mode.
   *
   * @var string
   */
  protected $viewMode;

  /**
   * Constructs a new SubVariationAjaxChangeEvent.
   *
   * @param \Drupal\commerce_sub\Entity\SubVariationInterface $sub_variation
   *   The sub variation.
   * @param \Drupal\Core\Ajax\AjaxResponse $response
   *   The ajax response.
   * @param string $view_mode
   *   The view mode used to render the sub variation.
   */
  public function __construct(SubVariationInterface $sub_variation, AjaxResponse $response, $view_mode = 'default') {
    $this->subVariation = $sub_variation;
    $this->response = $response;
    $this->viewMode = $view_mode;
  }

  /**
   * The sub variation.
   *
   * @return \Drupal\commerce_sub\Entity\SubVariationInterface
   *   The sub variation.
   */
  public function getSubVariation() {
    return $this->subVariation;
  }

  /**
   * The ajax response.
   *
   * @return \Drupal\Core\Ajax\AjaxResponse
   *   The ajax reponse.
   */
  public function getResponse() {
    return $this->response;
  }

  /**
   * The view mode used to render the sub variation.
   *
   * @return string
   *   The view mode.
   */
  public function getViewMode() {
    return $this->viewMode;
  }

}
