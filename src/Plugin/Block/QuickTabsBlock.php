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
    $build['#markup'] = 'Hello World';
    
    $qt = \Drupal::service('entity.manager')->getStorage('quicktabs_instance')->load($block_id);
    //print '<pre>';
    //print_r($qt);
    //die(__FILE__ . $block_id);

    return $build;
  }
}
