<?php
/**
 * @file
 * Contains \Drupal\quicktabs\Entity\QuickTabsInstance.
 */

namespace Drupal\quicktabs\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

Use Drupal\Core\Link;
Use Drupal\Core\Url;
Use Drupal\Core\Template\Attribute;

/**
 * Defines the QuickTabsInstance entity.
 *
 * The QuickTabsInstnace entity stores information about a quicktab.
 *
 * @ConfigEntityType(
 *   id = "quicktabs_instance",
 *   label = @Translation("Quick Tabs"),
 *   module = "quicktabs",
 *   handlers = {
 *     "list_builder" = "Drupal\quicktabs\QuickTabsInstanceListBuilder",
 *     "form" = {
 *       "add" = "Drupal\quicktabs\Form\QuickTabsInstanceEditForm",
 *       "edit" = "Drupal\quicktabs\Form\QuickTabsInstanceEditForm",
 *       "delete" = "Drupal\quicktabs\Form\QuickTabsInstanceDeleteForm",
 *       "duplicate" = "Drupal\quicktabs\Form\QuickTabsInstanceDuplicateForm",
 *     },
 *   },
 *   config_prefix = "quicktabs_instance",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label"
 *   },
 *   links = {
 *     "edit" = "/admin/structure/quicktabs/{quicktabs_instance}/edit",
 *     "add" = "/admin/structure/quicktabs/add",
 *     "delete" = "/admin/structure/quicktabs/{quicktabs_instance}/delete",
 *     "duplicate" = "/admin/structure/quicktabs/{quicktabs_instance}/duplicate"
 *   },
 *   config_export = {
 *     "id" = "id",
 *     "label" = "label",
 *     "renderer" = "renderer",
 *     "style" = "style",
 *     "ajax" = "ajax",
 *     "hide_empty_tabs" = "hide_empty_tabs",
 *     "default_tab" = "default_tab",
 *     "configuration_data" = "configuration_data"
 *   },
 *   admin_permission = "administer quicktabs",
 * )
 */
class QuickTabsInstance extends ConfigEntityBase implements QuickTabsInstanceInterface {

  /**
   * The QuickTabs Instance ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The label of the QuickTabs Instance.
   *
   * @var string
   */
  protected $label;

  /**
   * The renderer of the QuickTabs Instance.
   *
   * @var string
   */
  protected $renderer;

  /**
   * The style of the QuickTabs Instance.
   *
   * @var string
   */
  protected $style;

  /**
   * Whether or not to use ajax.
   *
   * @var bool
   */
  protected $ajax;

  /**
   * whether or not to hide empty tabs.
   *
   * @var bool
   */
  protected $hide_empty_tabs;

  /**
   * whether or not to hide empty tabs.
   *
   * @var bool
   */
  protected $default_tab;

  /**
   * required to render this instance.
   *
   * @var array
   */
  protected $configuration_data;

  /**
   * {@inheritdoc}
   */
  public function getLabel() {
    return $this->label;
  }

  /**
   * {@inheritdoc}
   */
  public function getRenderer() {
    return $this->renderer;
  }

  /**
   * {@inheritdoc}
   */
  public function getStyle() {
    return $this->style;
  }

  /**
   * {@inheritdoc}
   */
  public function isAjax() {
    return $this->ajax;
  }

  /**
   * {@inheritdoc}
   */
  public function getHideEmptyTabs() {
    return $this->hide_empty_tabs;
  }
  
  /**
   * {@inheritdoc}
   */
  public function getDefaultTab() {
    return isset($this->default_tab) ? $this->default_tab : 0;
  }

  /**
   * {@inheritdoc}
   */
  public function getConfigurationData() {
    return $this->configuration_data;
  }

  /**
   * {@inheritdoc}
   */
  public function setConfigurationData($configuration_data) {
    $this->configuration_data = $configuration_data;
  }

  /**
   * Returns a render array to be used in a block or page.
   *
   * @return array a render array
   */
  public function getRenderArray() {
    $qt_id = $this->id();
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

    foreach ($this->getConfigurationData() as $index => $tab) {
      // Build the pages //////////////////////////////////////
      if ($this->isAjax()) {
        if ($this->getDefaultTab() == $index) {
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

      if ($this->getDefaultTab() != $index) {
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
      if ($this->getDefaultTab() == $index) {
        $wrapper_attributes['class'] = array('active');
      }

      $link_classes = array();
      if ($this->isAjax()) {
        $link_classes[] = 'use-ajax';

        if ($this->getDefaultTab() == $index) {
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

  /**
   * Loads a quicktabs_instance from configuration and returns it.
   *
   * @param string $id
   *   The qti ID to load.
   *
   * @return \Drupal\quicktabs\Entity\QuickTabsInstance
   *   The loaded entity.
   */
  public static function getQuicktabsInstance($id) {
    $qt = \Drupal::service('entity.manager')->getStorage('quicktabs_instance')->load($id);
    return $qt;
  }
}
