<?php
/**
 * @file
 * Contains Drupal\src\QuicktabsManager.php
 */

namespace Drupal\quicktabs;

use \Drupal\Core\Plugin\DefaultPluginManager;
use \Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Cache\CacheBackendInterface;

/**
 * Quicktabs Plugin Manager.
 */
class QuicktabsManager extends DefaultPluginManager {

  /**
   * @param \Traversable $namespaces
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct('Plugin/QuickContent', $namespaces,  $module_handler, 'Drupal\quicktabs\QuicktabContentInterface', 'Drupal\quicktabs\Annotation\QuicktabFormat');

    $this->alterInfo('quicktabs_content_alter_info');
    $this->setCacheBackend($cache_backend , 'quicktabs_content');
  }
}