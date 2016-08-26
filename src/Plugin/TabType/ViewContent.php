<?php
/**
 * @file
 * Contains \Drupal\quicktabs\Plugin\TabType\ViewContent.
 */

namespace Drupal\quicktabs\Plugin\TabType;

use Drupal\quicktabs\TabTypeBase;
use Drupal\views\Views;

/**
 * Provides a 'view content' tab type.
 *
 * @TabType(
 *   id = "view_content",
 *   name = @Translation("view"),
 * )
 */
class ViewContent extends TabTypeBase {

  /**
   * {@inheritdoc}
   */
  public function optionsForm($tab) {
    $plugin_id = $this->getPluginDefinition()['id'];
    $views = $this->getViews();
    $views_keys = array_keys($views);
    $selected_view = (isset($tab['content'][$plugin_id]['options']['vid']) ? $tab['content'][$plugin_id]['options']['vid'] : (isset($views_keys[0]) ? $views_keys[0] : ''));

    $form = array();
    $form['vid'] = array(
      '#type' => 'select',
      '#options' => $views,
      '#default_value' => $selected_view,
      '#title' => t('Select a view'),
      '#ajax' => array(
        'callback' => 'Drupal\quicktabs\Form\QuickTabsInstanceEditForm::viewsDisplaysAjaxCallback',
        'event' => 'change',
        'progress' => array(
          'type' => 'throbber',
          'message' => 'Please wait...',
        ), 
        'effect' => 'fade',
      ),
    );
    $form['display'] = array(
      '#type' => 'select',
      '#title' => 'display',
      '#options' => ViewContent::getViewDisplays($selected_view),
      '#default_value' => isset($tab['content'][$plugin_id]['options']['display']) ? $tab['content'][$plugin_id]['options']['display'] : '',
      '#prefix' => '<div id="view-display-dropdown-' . $tab['delta'] . '">',
      '#suffix' => '</div>'
    );
    $form['args'] = array(
      '#type' => 'textfield',
      '#title' => 'arguments',
      '#size' => '40',
      '#required' => FALSE,
      '#default_value' => isset($tab['content'][$plugin_id]['options']['args']) ? $tab['content'][$plugin_id]['options']['args'] : '',
      '#description' => t('Additional arguments to send to the view as if they were part of the URL in the form of arg1/arg2/arg3. You may use %0, %1, ..., %N to grab arguments from the URL.'),
    );
    
    return $form;
  }
  
  /**
   * {@inheritdoc}
   */
  public function render(array $tab) {
    $options = $tab['content'][$tab['type']]['options'];
    $args = empty($options['args']) ? array() : array_map('trim', explode(',', $options['args']));
    $view = Views::getView($options['vid']);
    $render = $view->buildRenderable($options['display'], $args);

    return $render;
  }

  /**
   * Ajax callback for the add tab and remove tab buttons.
   */
  public function getDisplaysCallback(array &$form, FormStateInterface $form_state) {
    // Instantiate an AjaxResponse Object to return.
    $ajax_response = new AjaxResponse();
    $ajax_response->addCommand(new HtmlCommand('#configuration-data-wrapper', $form['configuration_data_wrapper']['configuration_data']));

    return $ajax_response;
  }
  
  private function getViews() {
    $views = array();
    foreach (Views::getEnabledViews() as $view_name => $view) {
      $views[$view_name] = $view->label() . ' (' . $view_name . ')';
    }

    ksort($views);
    return $views;
  }

  public function getViewDisplays($view_name) {
    $displays = array();
    if (empty($view_name)) {
      return $displays;
    }

    $view = \Drupal::entityManager()->getStorage('view')->load($view_name);
    foreach ($view->get('display') as $id => $display) {
      $enabled = !empty($display['display_options']['enabled']) || !array_key_exists('enabled', $display['display_options']);
      if ($enabled) {
        $displays[$id] = $id .': '. $display->display_title;
      }
    }

    return $displays;
  }
}
