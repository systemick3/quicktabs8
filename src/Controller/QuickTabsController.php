<?php
/**
 * @file
 * Contains \Drupal\quicktabs\Controller\QuickTabs.
 */

namespace Drupal\quicktabs\Controller;

use Drupal\Core\Controller\ControllerBase;
/**
 * Class QuickTabs
 */
class QuickTabsController extends ControllerBase {
  /**
   * {@inheritdoc}
   */
  public function content() {
    $build = array(
      '#type' => 'markup',
      //'#markup' => '<p>' . t('Each Quicktabs instance has a corresponding block that is managed on the <a href="!blocks">blocks administration page</a>.', array('!blocks' => \Drupal::Url('block.admin_display'))). '</p>',
    );
    return $build;
  }
}
