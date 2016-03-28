<?php

namespace Drupal\quicktabs;

/**
 * Each QuickSet object has a "contents" property which is an array of objects
 * that implement the QuickContentRenderableInterface.
 */
interface QuickContentRenderableInterface {

  /**
   * Returns the short type name of the content plugin, e.g. 'block', 'node',
   * 'prerendered'.
   */
  public static function getType();

  /**
   * Returns the tab title.
   */
  public function getTitle();

  /**
   * Returns an array of settings specific to the type of content.
   */
  public function getSettings();

  /**
   * Renders the content.
   *
   * @param $hide_emtpy If set to true, then the renderer should return an empty
   * array if there is no content to display, for example if the user does not
   * have access to the requested content.
   *
   * @param $args Used during an ajax call to pass in the settings necessary to
   * render this type of content.
   */
  public function render($hide_empty = FALSE, $args = array());

  /**
   * Returns an array of keys to use for constructing the correct arguments for
   * an ajax callback to retrieve content of this type. The order of the keys
   * returned affects the order of the args passed in to the render method when
   * called via ajax (see the render() method above).
   */
  public function getAjaxKeys();

  /**
   * Returns an array of keys, sufficient to represent the content uniquely.
   */
  public function getUniqueKeys();

}
