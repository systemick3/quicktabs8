<?php
/**
 * @file
 * Contains \Drupal\quicktabs\Plugin\TabRenderer\AccordianTabs.
 */

namespace Drupal\quicktabs\Plugin\TabRenderer;

use Drupal\quicktabs\TabRendererBase;
use Drupal\quicktabs\Entity\QuickTabsInstance;

/**
 * Provides an 'AccordianTabs' tab renderer.
 *
 * @TabRenderer(
 *   id = "accordian_tabs",
 *   name = @Translation("accordian"),
 * )
 */
class AccordianTabs extends TabTypeRenderer {
  
  /**
   * {@inheritdoc}
   */
  /*public function optionsForm($tab) {
  }*/

  /**
   * {@inheritdoc}
   */
  public function render(QuickTabsInstance $instance) {
    /*$options = $tab['content'][$tab['type']]['options'];
    if (strpos($options['bid'], 'block_content') !== FALSE) {
      $parts = explode(':', $options['bid']);
      $entity_manager = \Drupal::service('entity.manager');
      $block = $entity_manager->loadEntityByUuid($parts[0], $parts[1]);
      $block_content = \Drupal\block_content\Entity\BlockContent::load($block->id());
      $render = \Drupal::entityManager()->getViewBuilder('block_content')->view($block_content);
    }
    else {
      $block_manager = \Drupal::service('plugin.manager.block');
      // You can hard code configuration or you load from settings.
      $config = [];
      $plugin_block = $block_manager->createInstance($options['bid'], $config);

      // Some blocks might implement access check.
      //$access_result = $plugin_block->access(\Drupal::currentUser());
      // Return empty render array if user doesn't have access.
      //if ($access_result->isForbidden()) {
        // You might need to add some cache tags/contexts.
        //return [];
      //}

      $render = $plugin_block->build();
    }

    return $render;*/

    return arrray();
  }
}
