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
  //public function optionsForm($delta, $qt) {
  public function optionsForm() {
    $tab = $this->settings;
    $form = array();
    $form['block']['bid'] = array(
      '#type' => 'select',
      '#options' => $this->getBlockOptions(),
      '#default_value' => isset($tab['bid']) ? $tab['bid'] : '',
      '#title' => t('Select a block'),
    );
    $form['block']['hide_title'] = array(
      '#type' => 'checkbox',
      '#title' => t('Hide the title of this block'),
      '#default_value' => isset($tab['hide_title']) ? $tab['hide_title'] : 1,
    );
    return $form;
  }

  private function getBlockOptions() {
    $config = \Drupal::config('system.theme');
    $default_theme = $config->get('default');
    $block_storage = \Drupal::entityManager()->getStorage('block');
    $entities = $block_storage->loadMultiple();
    $blocks = [];
    foreach ($entities as $entity_id => $entity) {
      if ($entity->getTheme() === $default_theme) {
        $definition = $entity->getPlugin()->getPluginDefinition();
        $blocks[$entity_id] = $entity->label() . ' (' . $definition['category'] . ')';
      }
    }
    
    return $blocks;
  }
}
