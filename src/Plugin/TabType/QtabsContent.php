<?php
/**
 * @file
 * Contains \Drupal\quicktabs\Plugin\TabType\QtabsContent.
 */

namespace Drupal\quicktabs\Plugin\TabType;

use Drupal\quicktabs\TabTypeBase;

/**
 * Provides a 'qtabs content' tab type.
 *
 * @TabType(
 *   id = "qtabs_content",
 *   name = @Translation("qtabs"),
 * )
 */
class QtabsContent extends TabTypeBase {

  /**
   * {@inheritdoc}
   */
  public function optionsForm() {
    return array();
  }
}
