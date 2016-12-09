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
  public function getLabel();
  public function getRenderer();
  public function getOptions();
  public function getHideEmptyTabs();
  public function getDefaultTab();
  public function getConfigurationData();
  public function setConfigurationData($configuration_data);
}

?>
