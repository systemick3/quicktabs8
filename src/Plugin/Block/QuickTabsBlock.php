<?php

/**
 * @file
 * Contains \Drupal\quicktabs\Plugin\Block\QuickTabsBlock.
 */

namespace Drupal\quicktabs\Plugin\Block;
use Drupal\block\BlockBase;

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
    return $build;
  }
}
