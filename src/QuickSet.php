<?php

namespace Drupal\quicktabs;

use Drupal\quicktabs\InvalidQuickSetException;

/**
 * A QuickSet object is an unrendered Quicktabs instance, essentially just a
 * container of content items, as defined by its configuration settings and the
 * array of content items it contains.
 */
class QuickSet {

  /**
   * The unique name of the QuickSet object.
   * This corresponds to the machine name as stored in the database or as defined
   * in code.
   * @var string
   */
  protected $name;

  /**
   * The contents array.
   * An array of objects that implement the QuickContentRenderableInterface.
   * @var array
   */
  protected $contents;

  /**
   * An array of settings controlling the behaviour of the QuickSet object. See
   * the getDefaultSettings() static function of this class for the full list of
   * settings.
   * @var array
   */
  protected $settings;


  /**
   * Accessors.
   */

  public function getName() {
    return $this->name;
  }

  public function getContents() {
    return $this->contents;
  }

  public function getSettings() {
    return $this->settings;
  }

  public function getTitle() {
    return isset($this->settings['title']) ? $this->translateString($this->settings['title'], 'title') : $this->name;
  }

  /**
   * Instantiate, populate and return a QuickSet object wrapped in a renderer.
   *
   * @param $name
   *   The unique name (machine name) of the QuickSet instance.
   *
   * @param $contents
   *   The array of content items, each one itself an array with at least a 'type'
   *   key, a 'title' key, and the other info necessary for that type.
   *
   * @param $renderer
   *   The plugin key for this renderer plugin
   *
   * @param $settings
   *   An array of settings determining the behaviour of this QuickSet instance.
   *
   */
  public static function QuickSetRendererFactory($name, $contents, $renderer, $settings) {
    ctools_include('plugins');
    $class = ctools_plugin_load_class('quicktabs', 'renderers', $renderer, 'handler');
    if ($class) {
      try {
        $qs = new self($name, $contents, $settings);
      }
      catch (InvalidQuickSetException $e) {
        watchdog('Quicktabs', $e->getMessage());
        return NULL;
      }
      return new $class($qs);
    }
  }

  /**
   * Returns a reference to an object that implements the QuickContentRenderableInterface.
   */
  public static function getContentRenderer($tab) {
    if ($tab['type'] == 'prerendered') {
      return new QuickPreRenderedContent($tab);
    }
    if ($content = QuickContent::factory($tab['type'], $tab)) {
      return $content;
    }
    return NULL;
  }

  /**
   * Static method to retrieve content from an ajax call. This is called by the
   * quicktabs_ajax() callback in quicktabs.module.
   */
  public static function ajaxRenderContent($type, $args) {
    if ($renderer = self::getContentRenderer(array('type' => $type))) {
      $output = $renderer->render(FALSE, $args);
      return !empty($output) ? drupal_render($output) : '';
    }
    return '';
  }

  /**
   * Ensure sensible default settings for each QuickSet object.
   */
  private static function getDefaultSettings() {
    return array(
      'title' => '<none>',
      'style' => 'nostyle',
      'hide_empty_tabs' => 0,
      'ajax' => 0,
      'default_tab' => 0,
      'options' => array(),
    );
  }

  /**
   * Constructor
   */
  public function __construct($name, $contents, $settings) {
    $this->name = $name;
    $this->contents = array();
    foreach ($contents as $key => $item) {
      // Instantiate a content renderer object and add it to the contents array.
      if ($renderer = self::getContentRenderer($item)) {
        $this->contents[$key] = $renderer;
      }
    }
    $default_settings = self::getDefaultSettings();
    $this->settings = array_merge($default_settings, $settings);

    $this->prepareContents();
    // Set the default style if necessary.
    if ($this->settings['style'] == 'default') {
      $this->settings['style'] = variable_get('quicktabs_tabstyle', 'nostyle');
    }
  }

  /**
   * Returns an ajax path to be used on ajax-enabled tab links.
   *
   * @param $index The index of the tab, i.e where it fits into the QuickSet
   * instance.
   *
   * @param $type The type of content we are providing an ajax path for.
   */
  public function getAjaxPath($index, $type) {
    return 'quicktabs/ajax/'. $this->name .'/'. $index . '/'. $type;
  }

  /**
   * Translates Quicktabs user-defined strings if the i18n module is
   * enabled.
   */
  public function translateString($string, $type = 'tab', $index = 0) {
    switch ($type) {
      case 'tab':
        $name = "tab:{$this->name}-{$index}:title";
        break;
      case 'title':
        $name = "title:{$this->name}";
        break;
    }
    return quicktabs_translate($name, $string);
  }

  /**
   * This method does some initial set-up of the tab contents, such as hiding
   * tabs with no content if the hide_empty_tabs option is set. It also makes sure
   * that prerendered contents are never attempted to be loaded via ajax.
   *
   * @throws InvalidQuickSetException if there are no contents to render.
   */
  protected function prepareContents() {
    if (!count($this->contents))  {
     // throw new InvalidQuickSetException('There are no contents to render.');
    }
    if ($this->settings['hide_empty_tabs'] && !$this->settings['ajax']) {
      // Check if any tabs need to be hidden because of empty content.
      $renderable_contents = 0;
      foreach ($this->contents as $key => $tab) {
        $contents = $tab->render(TRUE);
        if (empty($contents)) {
          // Rather than removing the item, we set it to NULL. This way we retain
          // the same indices across tabs, so that permanent links to particular
          // tabs can be relied upon.
          $this->contents[$key] = NULL;
          // The default tab must not be a hidden tab.
          if ($this->settings['default_tab'] == $key) {
            $this->settings['default_tab'] = ($key + 1) % count($this->contents);
          }
        }
        else {
          $renderable_contents++;
        }
      }
      if (!$renderable_contents)  {
      //  throw new InvalidQuickSetException('There are no contents to render.');
      }
    }
    elseif ($this->settings['ajax']) {
      // Make sure that there is at most 1 prerendered tab and it is the default tab.
      // Prerendered content cannot be rendered via ajax.
      $has_prerendered = FALSE; // keep track of whether we have found a prerendered tab.
      foreach ($this->contents as $key => $tab) {
        $type = $tab->getType();
        if ($type == 'prerendered') {
          if (!$has_prerendered) {
            $has_prerendered = TRUE;
            $this->settings['default_tab'] = $key;
            // In the case of a direct link to a different tab, the 'default_tab'
            // will be overridden, so we need to make sure it does not attempt
            // to load a pre-rendered tab via ajax. Turn ajax option off.
            if ($this->getActiveTab() !== $key) {
              $this->settings['ajax'] = 0;
            }
          }
          else {
            // We are on a second custom tab and the ajax option is set, we cannot
            // render custom tabs via ajax, so we skip out of the loop, set the
            // ajax option to off, and call the method again.
            $this->settings['ajax'] = 0;
            $this->prepareContents();
            return;
          }
        }
      }
    }
  }

  /**
   * Returns the active tab for a given Quicktabs instance. This could be coming
   * from the URL or just from the settings for this instance. If neither, it
   * defaults to 0.
   */
  public function getActiveTab() {
    $active_tab = isset($this->settings['default_tab']) ? $this->settings['default_tab'] : key($this->contents);
    $active_tab = isset($_GET['qt-' . $this->name]) ? $_GET['qt-' . $this->name] : $active_tab;
    $active_tab = (isset($active_tab) && isset($this->contents[$active_tab])) ? $active_tab : QUICKTABS_DELTA_NONE;
    return $active_tab;
  }
}
