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
    $views = $this->getViews();
    $views_keys = array_keys($views);
    $selected_view = (isset($tab['vid']) ? $tab['vid'] : (isset($views_keys[0]) ? $views_keys[0] : ''));

    $form = array();
    $form['view']['vid'] = array(
      '#type' => 'select',
      '#options' => $views,
      '#default_value' => $selected_view,
      '#title' => t('Select a view'),
      '#ajax' => array(
        'callback' => '_quicktabs_replace_view_displays_callback',
      ),
    );
    $form['view']['display'] = array(
      '#type' => 'select',
      '#title' => 'display',
      '#options' => $this->getViewDisplays($selected_view),
      '#default_value' => isset($tab['display']) ? $tab['display'] : '',
      '#prefix' => '<div id="view-display-dropdown-' . $delta . '">',
      '#suffix' => '</div>'
    );
    $form['view']['args'] = array(
      '#type' => 'textfield',
      '#title' => 'arguments',
      '#size' => '40',
      '#required' => FALSE,
      '#default_value' => isset($tab['args']) ? $tab['args'] : '',
      '#description' => t('Additional arguments to send to the view as if they were part of the URL in the form of arg1/arg2/arg3. You may use %0, %1, ..., %N to grab arguments from the URL.'),
    );
    return $form;
  }

  private function getViews() {
    $views = array();
    foreach (Views::getEnabledViews() as $view_name => $view) {
      $views[$view_name] = $view->label() . ' (' . $view_name . ')';
    }

    ksort($views);
    return $views;
  }

  private function getViewDisplays($view_name) {
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
