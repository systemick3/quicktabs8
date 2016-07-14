<?php
/**
 * @file
 * Contains \Drupal\quicktabs\Entity\QuickTabsInstanceInterface.
 */
 
namespace Drupal\quicktabs\Entity;
 
use Drupal\Core\Config\Entity\ConfigEntityInterface;
 
/**
 * Interface for QuickTabsInstance.
 */
interface QuickTabsInstanceInterface extends ConfigEntityInterface {
  public function getRenderer();
  public function getStyle();
  public function isAjax();
  public function getHideEmptyTabs();
}

?>
