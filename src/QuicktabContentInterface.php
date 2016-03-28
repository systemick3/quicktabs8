<?php
/**
 * @file
 * Contains \Drupal\quicktabs\QuickTabContentInterface.
 */

namespace Drupal\quicktabs;
use Drupal\Core\Form\FormStateInterface;

/**
 * Helper interface to other Content Plugins.
 */
interface QuicktabContentInterface {

  /**
   * @return string
   */
  public static function getType();

  /**
   * @param $delta
   * @param $qt
   * @param array $form
   * test description.
   * @return array
   */
  public function optionsForm($delta, $qt, $form);

  /**
   * @param bool $hide_empty
   * @param array $args
   * @return array
   */
  public function render($hide_empty = FALSE, $args = array());

  /**
   * @return array
   */
  public function getAjaxKeys();

  /**
   * @return array
   */
  public function getUniqueKeys();
}