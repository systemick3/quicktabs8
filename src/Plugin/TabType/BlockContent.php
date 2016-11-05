<?php
/**
 * @file
 * Contains \Drupal\quicktabs\Plugin\TabType\BlockContent.
 */

namespace Drupal\quicktabs\Plugin\TabType;

use Drupal\quicktabs\TabTypeBase;
use Drupal\block\BlockListBuilder;

/**
 * Provides a 'blcok content' tab type.
 *
 * @TabType(
 *   id = "block_content",
 *   name = @Translation("block"),
 * )
 */
class BlockContent extends TabTypeBase {
  
  /**
   * {@inheritdoc}
   */
  public function optionsForm($tab) {
    $plugin_id = $this->getPluginDefinition()['id'];
    $form = array();
    $form['bid'] = array(
      '#type' => 'select',
      '#options' => $this->getBlockOptions(),
      '#default_value' => isset($tab['content'][$plugin_id]['options']['bid']) ? $tab['content'][$plugin_id]['options']['bid'] : '',
      '#title' => t('Select a block'),
    );
    $form['block_title'] = array(
      '#type' => 'textfield',
      '#default_value' => isset($tab['content'][$plugin_id]['options']['block_title']) ? $tab['content'][$plugin_id]['options']['block_title'] : '',
      '#title' => t('Block Title'),
      //'#element_validate' => array('quicktabs_callback_element_validate'),
    );
    $form['display_title'] = array(
      '#type' => 'checkbox',
      '#title' => t('Display block title'),
      '#default_value' => isset($tab['content'][$plugin_id]['options']['display_title']) ? $tab['content'][$plugin_id]['options']['display_title'] : 0,
    );
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function render(array $tab) {
    $options = $tab['content'][$tab['type']]['options'];

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

    return $render;
  }

  private function getBlockOptions() {
    $block_manager = \Drupal::service('plugin.manager.block');
    $context_repository = \Drupal::service('context.repository');

    // Only add blocks which work without any available context.
    $definitions = $block_manager->getDefinitionsForContexts($context_repository->getAvailableContexts());
    // Order by category, and then by admin label.
    $definitions = $block_manager->getSortedDefinitions($definitions);

    $blocks = [];
    foreach ($definitions as $block_id => $definition) {
      $blocks[$block_id] = $definition['admin_label'] . ' (' . $definition['provider'] . ')';
    }
    
    return $blocks;
  }
}
