<?php
/**
 * @file
 * Contains \Drupal\quicktabs\Entity\QuickTabsInstance.
 */

namespace Drupal\quicktabs\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;


/**
 * Defines the QuickTabsInstance entity.
 *
 * The QuickTabsInstnace entity stores information about a quicktab.
 *
 * @ConfigEntityType(
 *   id = "quicktabs_instance",
 *   label = @Translation("Quicktabs"),
 *   module = "quicktabs",
 *   handlers = {
 *     "list_builder" = "Drupal\quicktabs\QuickTabsInstanceListBuilder",
 *     "form" = {
 *       "add" = "Drupal\quicktabs\Form\QuickTabsInstanceForm",
 *       "edit" = "Drupal\quicktabs\Form\QuickTabsInstanceForm",
 *       "delete" = "Drupal\quicktabs\Form\QuicktabDeleteForm",
 *       "clone" = "Drupal\quicktabs\Form\QuicktabCloneForm",
 *       "export" = "Drupal\quicktabs\Form\QuicktabExportForm"
 *     },
 *   },
 *   config_prefix = "quicktabs_instance",
 *   entity_keys = {
 *     "id" = "id",
 *     "title" = "title"
 *   },
 *   links = {
 *     "edit" = "/admin/structure/quicktabs/{quicktabs_instance}/edit",
 *     "add" = "/admin/structure/quicktabs/add",
 *     "delete" = "/admin/structure/quicktabs/{quicktabs_instance}/delete",
 *     "clone" = "/admin/structure/quicktabs/{quicktabs_instance}/clone",
 *     "export" = "/admin/structure/quicktabs/{quicktabs_instance}/export"
 *   },
 *   config_export = {
 *     "id" = "id",
 *     "label" = "label",
 *     "renderer" = "renderer",
 *     "style" = "style",
 *     "ajax" = "ajax",
 *     "hide_empty_tabs" = "hide_empty_tabs"
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
}
