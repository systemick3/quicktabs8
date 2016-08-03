<?php
/**
 * @file
 * Contains \Drupal\quicktabs\Plugin\TabType\CallbackContent.
 */

namespace Drupal\quicktabs\Plugin\TabType;

use Drupal\quicktabs\TabTypeBase;

/**
 * Provides a 'callback content' tab type.
 *
 * @TabType(
 *   id = "callback_content",
 *   name = @Translation("callback"),
 * )
 */
class CallbackContent extends TabTypeBase {

  /**
   * {@inheritdoc}
   */
  public function optionsForm($tab) {
    $form = array();
    $form['callback']['path'] = array(
      '#type' => 'textfield',
      '#default_value' => isset($tab['path']) ? $tab['path'] : '',
      '#title' => t('Path'),
      '#element_validate' => array('quicktabs_callback_element_validate'),
    );
    return $form;
  }
}
