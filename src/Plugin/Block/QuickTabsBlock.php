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
    $build['pages'] = array();
    $build['pages']['#theme_wrappers'] = array(
      'container' => array(
        '#attributes' => array(
          'class' => array('quicktabs-main'),
          'id' => 'quicktabs-container-quicktabs',
        ),
      ),
    );
    $titles = array();
    $block_id = $this->getDerivativeId();
    
    $type = \Drupal::service('plugin.manager.tab_type');
    $plugin_definitions = $type->getDefinitions();
    $qt = \Drupal::service('entity.manager')->getStorage('quicktabs_instance')->load($block_id);
    $current_path = \Drupal::service('path.current')->getPath();
    $tab_page = 0;
    $tab_pages = array();
    foreach ($qt->getConfigurationData() as $index => $tab) {
      $tab['tab_page'] = $tab_page;
      $options = array(
        'query' => array('qt-quicktabs' => $tab_page),
        'fragment' => 'qt-quicktabs',
        'attributes' => array('id' => 'quicktabs-tab-quicktabs-' . $tab_page),
      );
      $titles[] = Link::fromTextAndUrl($tab['title'], Url::fromUri('internal:' . $current_path, $options));
      $object = $type->createInstance($tab['type']);
      $render = $object->render($tab);

      if ($qt->getDefaultTab() != $tab_page) {
        $render['#theme_wrappers']['container']['#attributes']['class'][] = 'quicktabs-hide';
      }

      $build['pages'][$index] = $render;
      $tab_pages[] = $tab;
      $tab_page++;
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
      'drupalSettings' => array(
        'quicktabs' => array(
          'qt_quicktabs' => array(
            'tabs' => $tab_pages,
          ),
        ),
      ),
    );

    $build['#theme_wrappers'] = array(
      'container' => array(
        '#attributes' => array(
          'class' => array('quicktabs-wrapper'),
          'id' => 'quicktabs-quicktabs',
        ),
      ),
    );
    
    return $build;
  }
}
