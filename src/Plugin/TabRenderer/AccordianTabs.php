<?php
/**
 * @file
 * Contains \Drupal\quicktabs\Plugin\TabRenderer\AccordianTabs.
 */

namespace Drupal\quicktabs\Plugin\TabRenderer;

use Drupal\quicktabs\TabRendererBase;
use Drupal\quicktabs\Entity\QuickTabsInstance;

/**
 * Provides an 'AccordianTabs' tab renderer.
 *
 * @TabRenderer(
 *   id = "accordion_tabs",
 *   name = @Translation("accordion"),
 * )
 */
class AccordianTabs extends TabRendererBase {
  
  /**
   * {@inheritdoc}
   */
  public function optionsForm(QuickTabsInstance $instance) {
    $options = $instance->getOptions()['accordion_tabs'];
    $form = array();
    $form['history'] = array(
      '#type' => 'checkbox',
      '#title' => 'History',
      '#description' => t('Store tab state in the URL allowing for browser back / forward and bookmarks.'),
      '#default_value' => ($options['history'] != NULL && $instance->getRenderer() == 'accordion_tabs') ? $options['history'] : 0,
    );
    $form['jquery_ui'] = array(
      '#type' => 'fieldset',
      '#title' => t('JQuery UI options'),
    );
    $form['jquery_ui']['autoHeight'] = array(
      '#type' => 'checkbox',
      '#title' => 'Autoheight',
      '#default_value' => ($options['jquery_ui']['autoHeight'] != NULL && $instance->getRenderer() == 'accordion_tabs') ? $options['jquery_ui']['autoHeight'] : 0,
    );
    $form['jquery_ui']['collapsible'] = array(
      '#type' => 'checkbox',
      '#title' => t('Collapsible'),
      '#default_value' => ($options['jquery_ui']['collapsible'] != NULL && $instance->getRenderer() == 'accordion_tabs') ? $options['jquery_ui']['collapsible'] : 0,
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

    // Add a wrapper
    $build['#theme_wrappers'] = array(
      'container' => array(
        '#attributes' => array(
          'class' => array('quicktabs-accordion'),
          'id' => 'quicktabs-' . $qt_id,
        ),
      ),
    );

    $tab_pages = [];
    foreach ($instance->getConfigurationData() as $index => $tab) {
      $qsid = 'quickset-' . $qt_id;
      $object = $type->createInstance($tab['type']);
      $render = $object->render($tab);
      $render['#prefix'] = '<h3><a href= "#' . $qsid . '_' . $index . '">' . $tab['title'] .'</a></h3><div>';
      $render['#suffix'] = '</div>';
      $build['pages'][$index] = $render;

      // Array of tab pages to pass as settings ////////////
      $tab['tab_page'] = $index;
      $tab_pages[] = $tab;
    }

    $build['#attached'] = array(
      'library' => array('quicktabs/quicktabs.bbq', 'quicktabs/quicktabs.accordion'),
      'drupalSettings' => array(
        'quicktabs' => array(
          'qt_' . $qt_id => array(
            'tabs' => $tab_pages,
            'active_tab' => $instance->getDefaultTab(),
            'options' => array(
              'active' => 1,
              //'autoHeight' => 0,
              'collapsible' => 1,
              //'header' => 'h3',
              //'event' => 'change',
            ),
          ),
        ),
      ),
    );

    return $build;
  }
}
