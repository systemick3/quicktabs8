<?php

/**
 * @file
 * Contains \Drupal\quicktabs\Plugin\Block\QuickTabsBlock.
 */

namespace Drupal\quicktabs\Plugin\Block;

use Drupal\Core\Block\BlockBase;
Use Drupal\Core\Url;
Use Drupal\Core\Link;

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
    $titles = array();
    $block_id = $this->getDerivativeId();
    
    $type = \Drupal::service('plugin.manager.tab_type');
    $plugin_definitions = $type->getDefinitions();
    $qt = \Drupal::service('entity.manager')->getStorage('quicktabs_instance')->load($block_id);
    $current_path = \Drupal::service('path.current')->getPath();
    $tab_number = 0;
    foreach ($qt->getConfigurationData() as $index => $tab) {
      $options = array(
        'query' => array('qt-quicktabs' => $tab_number),
        'fragment' => 'qt-quicktabs',
        'attributes' => array('id' => 'quicktabs-tab-quicktabs-' . $tab_number),
      );
      $titles[] = Link::fromTextAndUrl($tab['title'], Url::fromUri('internal:' . $current_path, $options));
      $object = $type->createInstance($tab['type']);
      $build[$index] = $object->render($tab['content'][$tab['type']]['options']);
      $tab_number++;
    }

    $tabs = array(
      '#theme' => 'item_list',
      '#items' => $titles,
      '#attributes' => array(
        'class' => array('quicktabs-tabs'), 
      ),
    );
    
    array_unshift($build, $tabs);
  
    $build['#attached'] = array(
      'library' => array('quicktabs/quicktabs'),
    );
    
    return $build;
  }
}
