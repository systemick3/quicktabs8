<?php
/**
 * @file
 * Contains \Drupal\quicktabs\Plugin\TabType\QtabsContent.
 */

namespace Drupal\quicktabs\Plugin\TabType;

use Drupal\quicktabs\TabTypeBase;

/**
 * Provides a 'qtabs content' tab type.
 *
 * @TabType(
 *   id = "qtabs_content",
 *   name = @Translation("qtabs"),
 * )
 */
class QtabsContent extends TabTypeBase {

  /**
   * {@inheritdoc}
   */
  public function optionsForm($tab) {
    $form = array();
    $tab_options = array();
    foreach (\Drupal::entityTypeManager()->getStorage('quicktabs_instance')->loadMultiple() as $machine_name => $entity) {
      // Do not offer the option to put a tab inside itself.
      if (!isset($tab['entity_id']) || $machine_name != $tab['entity_id']) {
        $tab_options[$machine_name] = $entity->label();
      }
    }
    $form['qtabs']['machine_name'] = array(
      '#type' => 'select',
      '#title' => t('Quicktabs instance'),
      '#description' => t('The Quicktabs instance to put inside this tab.'),
      '#options' => $tab_options,
      '#default_value' => isset($tab['machine_name']) ? $tab['machine_name'] : '',
    );
    return $form;
  }
}
