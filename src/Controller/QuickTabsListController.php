<?php
/**
 * @file
 * Contains \Drupal\quicktabs\Controller\QuickTabsListController.
 */

namespace Drupal\quicktabs\Controller;

use Drupal\Core\Controller\ControllerBase;
/**
 * Class QuickTabsListController
 */
class QuickTabsListController extends ControllerBase {
  /**
   * {@inheritdoc}
   */
  public function content() {
    $build = array(
      '#type' => 'markup',
      '#markup' => t('Hello World'),
    );
    return $build;
  }
}
