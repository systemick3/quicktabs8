<?php
/**
 * @file
 * Contains \Drupal\quicktabs\Plugin\TabRenderer\QuickTabs.
 */

namespace Drupal\quicktabs\Plugin\TabRenderer;

use Drupal\quicktabs\TabRendererBase;
use Drupal\quicktabs\Entity\QuickTabsInstance;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\Core\Template\Attribute;

/**
 * Provides a 'QuickTabs' tab renderer.
 *
 * @TabRenderer(
 *   id = "quick_tabs",
 *   name = @Translation("quicktabs"),
 * )
 */
class QuickTabs extends TabRendererBase {
  
  /**
   * {@inheritdoc}
   */
  public function optionsForm(QuickTabsInstance $instance) {
    $options = $instance->getOptions()['quick_tabs'];
    $form = array();
    $form['ajax'] = array(
      '#type' => 'radios',
      '#title' => t('Ajax'),
      '#options' => array(
        TRUE => t('Yes') . ': ' . t('Load only the first tab on page view'),
        FALSE => t('No') . ': ' . t('Load all tabs on page view.'),
      ),
      '#default_value' => ($instance->getRenderer() == 'quick_tabs' && $options['ajax'] !== NULL) ? $options['ajax'] : 0,
      '#description' => t('Choose how the content of tabs should be loaded.<p>By choosing "Yes", only the first tab will be loaded when the page first viewed. Content for other tabs will be loaded only when the user clicks the other tab. This will provide faster initial page loading, but subsequent tab clicks will be slower. This can place less load on a server.</p><p>By choosing "No", all tabs will be loaded when the page is first viewed. This will provide slower initial page loading, and more server load, but subsequent tab clicks will be faster for the user. Use with care if you have heavy views.</p><p>Warning: if you enable Ajax, any block you add to this quicktabs block will be accessible to anonymous users, even if you place role restrictions on the quicktabs block. Do not enable Ajax if the quicktabs block includes any blocks with potentially sensitive information.</p>'),
      '#weight' => -6,
    );
    return $form;
  }

  /**
   * Returns a render array to be used in a block or page.
   *
   * @return array a render array
   */
  public function render(QuickTabsInstance $instance) {
    $qt_id = $instance->id();
    $type = \Drupal::service('plugin.manager.tab_type');

    // The render array used to build the block
    $build = array();
    $build['pages'] = array();
    $build['pages']['#theme_wrappers'] = array(
      'container' => array(
        '#attributes' => array(
          'class' => array('quicktabs-main'),
          'id' => 'quicktabs-container-' . $qt_id,
        ),
      ),
    );

    // Pages of content that will be shown or hidden
    $tab_pages = array();

    // Tabs used to show/hide content
    $titles = array();

    foreach ($instance->getConfigurationData() as $index => $tab) {
      // Build the pages //////////////////////////////////////
      if ($instance->isAjax()) {
        if ($instance->getDefaultTab() == $index) {
          $object = $type->createInstance($tab['type']);
          $render = $object->render($tab);
        }
        else {
          $render = array('#markup' => 'Loading content ...');
        }
      }
      else {
        $object = $type->createInstance($tab['type']);
        $render = $object->render($tab);
      }

      $classes = array('quicktabs-tabpage');

      if ($instance->getDefaultTab() != $index) {
        $classes[] = 'quicktabs-hide';
      }

      $attributes = new Attribute(array('id' => 'quicktabs-tabpage-' . $qt_id . '-' . $index));
      $attributes['class'] = $classes;
      $render['#prefix'] = '<div ' . $attributes . '>';
      $render['#suffix'] = '</div>';

      $build['pages'][$index] = $render;

      // Build the tabs ///////////////////////////////
      $options = array(
        'query' => array('qt-quicktabs' => $index),
        'fragment' => 'qt-quicktabs',
        'attributes' => array('id' => 'quicktabs-tab-' . $qt_id . '-' . $index),
      );
      $wrapper_attributes = array();
      if ($instance->getDefaultTab() == $index) {
        $wrapper_attributes['class'] = array('active');
      }

      $link_classes = array();
      if ($instance->isAjax()) {
        $link_classes[] = 'use-ajax';

        if ($instance->getDefaultTab() == $index) {
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
              'instance' => $qt_id,
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
          'qt_' . $qt_id => array(
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
          'id' => 'quicktabs-' . $qt_id,
        ),
      ),
    );

    return $build;
  }
}
