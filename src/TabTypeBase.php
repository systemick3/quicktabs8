<?php
/**
 * @file
 * Contains \Drupal\quicktabs\TabTypeBase.
 */

namespace Drupal\quicktabs;

use Drupal\Component\Plugin\PluginBase;

abstract class TabTypeBase extends PluginBase implements TabTypeInterface {

  public function getName() {
    return $this->pluginDefinition['name'];
  }

  /**
   * {@inheritdoc}
   */
  abstract public function optionsForm($tab);
  
  /**
   * {@inheritdoc}
   */
  abstract public function render(array $options);
}
