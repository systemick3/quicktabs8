<?php
/**
 * @file
 * Contains \Drupal\quicktabs\Plugin\TabType\NodeContent.
 */

namespace Drupal\quicktabs\Plugin\TabType;

use Drupal\quicktabs\TabTypeBase;

/**
 * Provides a 'node content' tab type.
 *
 * @TabType(
 *   id = "node_content",
 *   name = @Translation("node"),
 * )
 */
class NodeContent extends TabTypeBase {

  /**
   * {@inheritdoc}
   */
  public function optionsForm($tab) {
    $plugin_name =  trim($this->getPluginDefinition()['name']);

    $form = array();
    $form['nid'] = array(
      '#type' => 'textfield',
      '#title' => t('Node'),
      '#description' => t('The node ID of the node.'),
      '#maxlength' => 10,
      '#size' => 20,
      '#default_value' => isset($tab['content'][$plugin_name]['options']['nid']) ? $tab['content'][$plugin_name]['options']['nid'] : '',
    );
    $view_modes = \Drupal::entityManager()->getViewModes('node');
    $options = array();
    foreach ($view_modes as $view_mode_name => $view_mode) {
      $options[$view_mode_name] = $view_mode['label'];
    }
    $form['view_mode'] = array(
      '#type' => 'select',
      '#title' => t('View mode'),
      '#options' => $options,
      '#default_value' => isset($tab['content'][$plugin_name]['options']['view_mode']) ? $tab['content'][$plugin_name]['options']['view_mode'] : 'full',
     );
     $form['hide_title'] = array(
      '#type' => 'checkbox',
      '#title' => t('Hide the title of this node'),
      '#default_value' => isset($tab['content'][$plugin_name]['options']['hide_title']) ? $tab['content'][$plugin_name]['options']['hide_title'] : 1,
    );
    return $form;
  }
}
