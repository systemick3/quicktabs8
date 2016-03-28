<?php

namespace Drupal\quicktabs;


/**
 * Abstract base class for content plugins.
 */
abstract class QuickContent implements QuickContentRenderableInterface {

  /**
   * Used as the title of the tab.
   * @var string
   */
  protected $title;

  /**
   * An array containing the information that defines the tab content, specific
   * to its type.
   * @var array
   */
  protected $settings;

  /**
   * A render array of the contents.
   * @var array
   */
  protected $rendered_content;


  /**
   * Constructor
   */
  public function __construct($item) {
    $this->title = isset($item['title']) ? $item['title'] : '';
    // We do not need to store title, type or weight in the settings array, which
    // is for type-specific settings.
    unset($item['title'], $item['type'], $item['weight']);
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
   * Instantiate a content type object.
   *
   * @param $name
   *   The type name of the plugin.
   *
   * @param $item
   *   An array containing the item definition
   *
   */
  public static function factory($name, $item) {
    ctools_include('plugins');
    if ($class = ctools_plugin_load_class('quicktabs', 'contents', $name, 'handler')) {
      // We now need to check the plugin's dependencies, to make sure they're installed.
      // This info has already been statically cached at this point so there's no
      // harm in making a call to ctools_get_plugins().
      $plugin = ctools_get_plugins('quicktabs', 'contents', $name);
      if (isset($plugin['dependencies'])) {
        foreach ($plugin['dependencies'] as $dep) {
          // If any dependency is missing we cannot instantiate our class.
          if (!Drupal::moduleHandler()->moduleExists($dep)) return NULL;
        }
      }
      return new $class($item);
    }
    return NULL;
  }

  /**
   * Method for returning the form elements to display for this tab type on
   * the admin form.
   *
   * @param $delta Integer representing this tab's position in the tabs array.
   *
   * @param $qt An object representing the Quicktabs instance that the tabs are
   * being built for.
   */
  abstract public function optionsForm($delta, $qt);

}
