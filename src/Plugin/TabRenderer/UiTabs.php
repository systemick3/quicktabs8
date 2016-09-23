<?php
/**
 * @file
 * Contains \Drupal\quicktabs\Plugin\TabRenderer\UiTabs.
 */

namespace Drupal\quicktabs\Plugin\TabRenderer;

use Drupal\quicktabs\TabRendererBase;
use Drupal\quicktabs\Entity\QuickTabsInstance;
use Drupal\Core\Template\Attribute;

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
  public function optionsForm($tab) {
    $form = array();
    $form['history'] = array(
      '#type' => 'checkbox',
      '#title' => 'History',
      '#description' => t('Store tab state in the URL allowing for browser back / forward and bookmarks.'),
      '#default_value' => (isset($qt->renderer) && $qt->renderer == 'ui_tabs' && isset($qt->options['history']) && $qt->options['history']),
    );
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function render(QuickTabsInstance $instance) {
    $qt_id = $instance->id();
    $type = \Drupal::service('plugin.manager.tab_type');

    // The render array used to build the block
    $build = array();
    $build['pages'] = array();
    
    // Pages of content that will be shown or hidden
    $tab_pages = array();

    // Tabs used to show/hide content
    $titles = array();

    foreach ($instance->getConfigurationData() as $index => $tab) {
      $object = $type->createInstance($tab['type']);
      $render = $object->render($tab);
      
      $classes = array('quicktabs-tabpage');

      $tab_num = $index + 1;
      $attributes = new Attribute(array('id' => 'qt-' . $qt_id . '-ui-tabs' . $tab_num));
      $render['#prefix'] = '<div ' . $attributes . '>';
      $render['#suffix'] = '</div>';

      $build['pages'][$index] = $render;

      $href = '#qt-'. $qt_id .'-ui-tabs' . $tab_num;
      $titles[] = array('#markup' => '<a href="'. $href .'">' . $tab['title'] .'</a>');
      
      $tab_pages[] = $tab;
    }
    
    // Add a wrapper
    $build['#theme_wrappers'] = array(
      'container' => array(
        '#attributes' => array(
          'class' => array('quicktabs-ui-wrapper'),
          'id' => 'quicktabs-' . $qt_id,
        ),
      ),
    );
    
    $tabs = array(
      '#theme' => 'item_list',
      '#items' => $titles,
    );

    // Add tabs to the build
    array_unshift($build, $tabs);
    
    // Attach js
    $build['#attached'] = array(
      'library' => array('quicktabs/quicktabs.bbq', 'quicktabs/quicktabs.ui'),
      'drupalSettings' => array(
        'quicktabs' => array(
          'qt_' . $qt_id => array(
            'tabs' => $tab_pages,
          ),
        ),
      ),
    );

    return $build;
  }
}
