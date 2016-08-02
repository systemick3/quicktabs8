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
    $form = array();
    $form['node']['nid'] = array(
      '#type' => 'textfield',
      '#title' => t('Node'),
      '#description' => t('The node ID of the node.'),
      '#maxlength' => 10,
      '#size' => 20,
      '#default_value' => isset($tab['nid']) ? $tab['nid'] : '',
    );
    $view_modes = \Drupal::entityManager()->getViewModes('node');
    $options = array();
    foreach ($view_modes as $view_mode_name => $view_mode) {
      $options[$view_mode_name] = $view_mode['label'];
    }
    $form['node']['view_mode'] = array(
      '#type' => 'select',
      '#title' => t('View mode'),
      '#options' => $options,
      '#default_value' => isset($tab['view_mode']) ? $tab['view_mode'] : 'full',
     );
     $form['node']['hide_title'] = array(
      '#type' => 'checkbox',
      '#title' => t('Hide the title of this node'),
      '#default_value' => isset($tab['hide_title']) ? $tab['hide_title'] : 1,
    );
    return $form;
  }
}
