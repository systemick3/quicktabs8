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
    $form['jquery_ui'] = array(
      '#type' => 'fieldset',
      '#title' => t('JQuery UI options'),
    );
    $form['jquery_ui']['collapsible'] = array(
      '#type' => 'checkbox',
      '#title' => t('Collapsible'),
      '#default_value' => ($options['jquery_ui']['collapsible'] != NULL && $instance->getRenderer() == 'accordion_tabs') ? $options['jquery_ui']['collapsible'] : 0,
    );
    $form['jquery_ui']['heightStyle'] = array(
      '#type' => 'radios',
      '#title' => t('JQuery UI HeightStyle'),
      '#options' => array(
        'auto' => t('auto'),
        'fill' => t('fill'),
        'content' => t('content'),
      ),
      '#default_value' => ($options['jquery_ui']['heightStyle'] != NULL && $instance->getRenderer() == 'accordion_tabs') ? $options['jquery_ui']['heightStyle'] : 'auto',
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

    $options = $instance->getOptions()['accordion_tabs'];
    $build['#attached'] = array(
      'library' => array('quicktabs/quicktabs.accordion'),
      'drupalSettings' => array(
        'quicktabs' => array(
          'qt_' . $qt_id => array(
            'tabs' => $tab_pages,
            'active_tab' => $instance->getDefaultTab(),
            'options' => array(
              'active' => (int)$instance->getDefaultTab(),
              'heightStyle' => $options['jquery_ui']['heightStyle'],
              'collapsible' => (int)$options['jquery_ui']['collapsible'],
            ),
          ),
        ),
      ),
    );

    return $build;
  }
}
