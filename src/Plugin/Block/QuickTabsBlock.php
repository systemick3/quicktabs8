<?php

/**
 * @file
 * Contains \Drupal\quicktabs\Plugin\Block\QuickTabsBlock.
 */

namespace Drupal\quicktabs\Plugin\Block;

use Drupal\Core\Block\BlockBase;
Use Drupal\Core\Url;
Use Drupal\Core\Link;
use Drupal\Core\Template\Attribute;

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
    $block_id = $this->getDerivativeId();
    
    $type = \Drupal::service('plugin.manager.tab_type');
    $qt = \Drupal::service('entity.manager')->getStorage('quicktabs_instance')->load($block_id);

    // The render array used to build the block
    $build = array();
    $build['pages'] = array();
    $build['pages']['#theme_wrappers'] = array(
      'container' => array(
        '#attributes' => array(
          'class' => array('quicktabs-main'),
          'id' => 'quicktabs-container-' . $block_id,
        ),
      ),
    );

    // Pages of content that will be shown or hidden
    $tab_pages = array();

    // Tabs used to show/hide content
    $titles = array();

    foreach ($qt->getConfigurationData() as $index => $tab) {
      // Build the pages //////////////////////////////////////
      if ($qt->isAjax()) {
        if ($qt->getDefaultTab() == $index) {
          $object = $type->createInstance($tab['type']);
          $render = $object->render($tab);
        }
        else {
          $render = array('#markup' => 'Content goes here');
        }
      }
      else {
        $object = $type->createInstance($tab['type']);
        $render = $object->render($tab);
      }

      $classes = array('quicktabs-tabpage');

      if ($qt->getDefaultTab() != $index) {
        $classes[] = 'quicktabs-hide';
      }

      $attributes = new Attribute(array('id' => 'quicktabs-tabpage-' . $block_id . '-' . $index));
      $attributes['class'] = $classes;
      $render['#prefix'] = '<div ' . $attributes . '>';
      $render['#suffix'] = '</div>';

      $build['pages'][$index] = $render;

      // Build the tabs ///////////////////////////////
      $options = array(
        'query' => array('qt-quicktabs' => $index),
        'fragment' => 'qt-quicktabs',
        'attributes' => array('id' => 'quicktabs-tab-' . $block_id . '-' . $index),
      );
      $wrapper_attributes = array();
      if ($qt->getDefaultTab() == $index) {
        $wrapper_attributes['class'] = array('active');
      }

      $link_classes = array();
      if ($qt->isAjax()) {
        $link_classes[] = 'use-ajax';
        
        if ($qt->getDefaultTab() == $index) {
          $link_classes[] = 'quicktabs-loaded';
        }
      }
      else {
        $link_classes[] = 'quicktabs-loaded';
      }

      $titles[] = array(
        '0' => Link::fromTextAndUrl(
          $tab['title'],
          Url::fromRoute(
            'quicktabs.ajax_content',
            array(
              'js' => 'nojs',
              'instance' => $block_id,
              'tab' => $index
            ),
            array(
              'attributes' => array(
                'class' => $link_classes,
              ),
            )
          )
        )->toRenderable(),
        '#wrapper_attributes' => $wrapper_attributes,
      );

      // Array of tab pages to pass as settings ////////////
      $tab['tab_page'] = $index;
      $tab_pages[] = $tab;
    }

    $tabs = array(
      '#theme' => 'item_list',
      '#items' => $titles,
      '#attributes' => array(
        'class' => array('quicktabs-tabs'), 
      ),
    );
    
    // Add tabs to the build
    array_unshift($build, $tabs);
  
    // Attach js
    $build['#attached'] = array(
      'library' => array('quicktabs/quicktabs'),
      'drupalSettings' => array(
        'quicktabs' => array(
          'qt_' . $block_id => array(
            'tabs' => $tab_pages,
          ),
        ),
      ),
    );

    // Add a wrapper
    $build['#theme_wrappers'] = array(
      'container' => array(
        '#attributes' => array(
          'class' => array('quicktabs-wrapper'),
          'id' => 'quicktabs-' . $block_id,
        ),
      ),
    );
    
    return $build;
  }
}
