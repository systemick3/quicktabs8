<?php
/**
 * @file
 * Contains \Drupal\quicktabs\TabRendererInterface.
 */

namespace Drupal\quicktabs;

use Drupal\Component\Plugin\PluginInspectionInterface;

/**
 * Defines an interface for tab renderer plugins.
 */
interface TabRendererInterface extends PluginInspectionInterface {

  /**
   * Return the name of the tab renderer.
   *
   * @return string
   */
  public function getName();
  
  /**
   * Return form elements used on the edit/add from.
   *
   * @return array
   */
  //public function optionsForm();
}
