<?php

/**
 * @file
 * Contains \Drupal\quicktabs\Plugin\QuickContent\QuickCallbackContent.php
 */

namespace Drupal\quicktabs\Plugin\QuickContent;

use Drupal\quicktabs\QuickContent;
use Drupal\quicktabs\QuicktabContentInterface;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class for tab content of type "callback" - this is for rendering the contents
 * of some menu callback function as tab content.
 * @QuicktabFormat{
 *   id = "quickcallbackcontent"
 */
class QuickCallbackContent extends QuickContent implements QuicktabContentInterface {

  /**
   * {@inheritdoc}
   */
  public static function getType() {
    return 'callback';
  }

  /**
   * @param $item
   */
  public function __construct($item) {
    parent::__construct($item);

    if (isset($item['path'])) {
      $url_args = arg();
      $path = $item['path'];

      foreach ($url_args as $id => $arg) {
        $path = str_replace("%$id", $arg, $path);
      }
      $path = preg_replace(',/?(%\d),', '', $path);
      if (!empty($path)) {
        $this->settings['ajax_path'] = rawurlencode($path);
      }
      else {
        $this->settings['ajax_path'] = '';
      }
      $this->settings['actual_path'] = $path;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function optionsForm($delta, $qt, $form_state) {
    $tab = $this->settings;
    $form = array();
    $form['callback']['path'] = array(
      '#type' => 'textfield',
      '#default_value' => isset($tab['path']) ? $tab['path'] : '',
      '#title' => t('Path'),
      '#element_validate' => array('quicktabs_callback_element_validate'),
    );
    $form['callback']['use_title'] = array(
      '#type' => 'checkbox',
      '#return_value'=>TRUE,
      '#title' => 'Use callback title',
      '#default_value' => isset($tab['use_title']) ? $tab['use_title'] : FALSE,
      '#description' => t('Should quicktabs use the rendered title of the callback?'),
    );
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function render($hide_empty = FALSE, $args = array()) {
    if ($this->rendered_content) {
      return $this->rendered_content;
    }
    $item = $this->settings;
    if (!empty($args)) {
      // The args have been passed in from an ajax request.
      // The first element of the args array is the qt_name, which we don't need
      // for this content type.
      array_shift($args);
      $item['actual_path'] = rawurldecode($args[0]);
      //$_GET['q'] = $item['actual_path'];
    }

    $output = array();
    if (isset($item['actual_path'])) {
      // Retain the current page title as we'll need to set it back after
      // calling menu_execute_active_handler().
      $request = \Drupal::request();
      $route_match = \Drupal::routeMatch();
      $page_title = \Drupal::service('title_resolver')->getTitle($request, $route_match);
      $request = \Drupal::service('request_stack');
      $subrequest = Request::create($item['actual_path'], 'GET', $request->query->all(), $request->cookies->all(), array(), $request->server->all());
      $response = \Drupal::service('http_kernel')->handle($subrequest, HttpKernelInterface::SUB_REQUEST);
      //$response = menu_execute_active_handler($item['actual_path'], FALSE);
      // Revert the page title.
      if($this->settings['use_title']) {
        $temp_request = \Drupal::request();
        $temp_route_match = \Drupal::routeMatch();
        $this->title = \Drupal::service('title_resolver')->getTitle($temp_request, $temp_route_match);
      }
      drupal_set_title($page_title);

      if (!is_array($response)) {
        if (is_int($response)) {
          if (MENU_ACCESS_DENIED == $response && !$hide_empty) {
            $output['#markup'] = theme('quicktabs_tab_access_denied', array('tab' => $item));
          }
          // For any other integer response form the menu callback, we'll just
          // return an empty array.
        }
        else {
          $output = array('#markup' => $response);
        }
      }
      else {
        $output = $response;
      }
    }
    $this->rendered_content = $output;
    return $output;
  }

  /**
   * {@inheritdoc}
   */
  public function getAjaxKeys() {
    return array('ajax_path');
  }

  /**
   * {@inheritdoc}
   */
  public function getUniqueKeys() {
    return array('ajax_path');
  }
}