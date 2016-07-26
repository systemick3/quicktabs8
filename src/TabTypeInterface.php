<?php
/**
 * @file
 * Contains \Drupal\quicktabs\TabTypeInterface.
 */

namespace Drupal\quicktabs;

use Drupal\Component\Plugin\PluginInspectionInterface;

/**
 * Defines an interface for tab type plugins.
 */
interface TabTypeInterface extends PluginInspectionInterface {

  /**
   * Return the name of the ice cream flavor.
   *
   * @return string
   */
  public function getName();
  
  /**
   * Return form elemets used on the edit/add from.
   *
   * @return array
   */
  //public function optionsForm();
}
