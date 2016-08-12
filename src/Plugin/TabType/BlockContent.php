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
    $plugin_name =  $this->getPluginDefinition()['name'];
    $form = array();
    $form['bid'] = array(
      '#type' => 'select',
      '#options' => $this->getBlockOptions(),
      '#default_value' => isset($tab['content'][$plugin_name->render()]['options']['bid']) ? $tab['content'][$plugin_name->render()]['options']['bid'] : '',
      '#title' => t('Select a block'),
    );
    $form['hide_title'] = array(
      '#type' => 'checkbox',
      '#title' => t('Hide the title of this block'),
      '#default_value' => isset($tab['content'][$plugin_name->render()]['options']['hide_title']) ? $tab['content'][$plugin_name->render()]['options']['hide_title'] : 0,
    );
    return $form;
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
