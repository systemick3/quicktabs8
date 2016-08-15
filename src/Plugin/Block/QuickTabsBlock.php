<?php

/**
 * @file
 * Contains \Drupal\quicktabs\Plugin\Block\QuickTabsBlock.
 */

namespace Drupal\quicktabs\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'QuickTabs' block.
 *
 * @Block(
 *   id = "quicktabs_block",
 *   admin_label = @Translation("QuickTabs Block"),
 *   category = @Translation("QuickTabs"),
 *   deriver = "Drupal\quicktabs\Plugin\Derivative\QuickTabsBlock"
 * )
 */

class QuickTabsBlock extends BlockBase {
  
  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = array();
    $block_id = $this->getDerivativeId();
    
    $type = \Drupal::service('plugin.manager.tab_type');
    $plugin_definitions = $type->getDefinitions();
    $qt = \Drupal::service('entity.manager')->getStorage('quicktabs_instance')->load($block_id);
    foreach ($qt->getConfigurationData() as $index => $tab) {
      $object = $type->createInstance($tab['type']);
      $build[$index] = $object->render($tab['content'][$tab['type']]['options']);
    }
    
    return $build;
  }
}
