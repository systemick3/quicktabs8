<?php
/**
 * @file
 * Contains \Drupal\quicktabs\Controller\QuicktabsController.
 */

namespace Drupal\quicktabs\Controller;

use Drupal\Core\Controller\ControllerBase;

class QuickTabsController extends ControllerBase {

  /**
   * {@inheritdoc}
   */
  public function content() {
    return array(
      //'#theme' => 'tagadelic_taxonomy_cloud',
      //'#tags' => $this->tags,
      //'#attached' => array(
        //'library' =>  array(
          //'tagadelic/base'
        //),
      //),
      //'#markup' => $this->t('Each Quicktabs instance has a corresponding block that is managed on the <a href="!blocks">blocks administration page</a>.', array('!blocks' => \Drupal::Url('block.admin_display'))),
      //'#markup' => $this->t('Each Quicktabs instance has a corresponding block that is managed on the '),
      //'#weight' => 1,
    );
  }
}
?>
