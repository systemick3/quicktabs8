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
    $plugin_id = $this->getPluginDefinition()['id'];
    $form = [];
    $form['path'] = array(
      '#type' => 'textfield',
      '#default_value' => isset($tab['content'][$plugin_id]['options']['path']) ? $tab['content'][$plugin_id]['options']['path'] : '',
      '#title' => t('Path'),
      '#element_validate' => array('quicktabs_callback_element_validate'),
    );
    return $form;
  }
  
  /**
   * {@inheritdoc}
   */
  public function render(array $options) {
    return array('#markup' => 'Calbback content');
  }
}
