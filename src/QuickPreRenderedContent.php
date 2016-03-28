<?php

namespace Drupal\quicktabs;


/**
 * This class implements the same interface that content plugins do but it is not
 * a content plugin. It is a special class for pre-rendered content which is used
 * when "custom" tabs are added to existing Quicktabs instances in a call to
 * quicktabs_build_quicktabs().
 */
class QuickPreRenderedContent implements QuickContentRenderableInterface {

  public static function getType() {
    return 'prerendered';
  }

  /**
   * Used as the title of the tab.
   * @var title
   */
  protected $title;

  /**
   * A render array of the contents.
   * @var array
   */
  protected $rendered_content;

  /**
   * An array containing the information that defines the tab content, specific
   * to its type.
   * @var array
   */
  protected $settings;


  /**
   * Constructor
   */
  public function __construct($item) {

    $contents = isset($item['contents']) ? $item['contents'] : array();
    if (!is_array($contents)) {
      $contents = array('#markup' => $contents);
    }
    $this->rendered_content = $contents;

    $this->title = isset($item['title']) ? $item['title'] : '';

    unset($item['title'], $item['contents']);
    $this->settings = $item;
  }

  /**
   * Accessor for the tab title.
   */
  public function getTitle() {
    return $this->title;
  }

  /**
   * Accessor for the tab settings.
   */
  public function getSettings() {
    return $this->settings;
  }


  /**
   * The render method simply returns the contents that were passed in and
   * stored during construction.
   */
  public function render($hide_empty = FALSE, $args = array()) {
    return $this->rendered_content;
  }

  /**
   * This content cannot be rendered via ajax so we don't return any ajax keys.
   */
  public function getAjaxKeys() {
    return array();
  }

  public function getUniqueKeys() {
    return array('class_suffix');
  }

}
