<?php
/**
 * @file
 * Contains \Drupal\quicktabs\Plugin\TabRenderer\UiTabs.
 */

namespace Drupal\quicktabs\Plugin\TabRenderer;

use Drupal\quicktabs\TabRendererBase;
use Drupal\quicktabs\Entity\QuickTabsInstance;

/**
 * Provides a 'ui tabs' tab renderer.
 *
 * @TabRenderer(
 *   id = "ui_tabs",
 *   name = @Translation("ui"),
 * )
 */
class UiTabs extends TabRendererBase {
  
  /**
   * {@inheritdoc}
   */
  /*public function optionsForm($tab) {
  }*/

  /**
   * {@inheritdoc}
   */
  public function render(QuickTabsInstance $instance) {
    return array();
  }
}
