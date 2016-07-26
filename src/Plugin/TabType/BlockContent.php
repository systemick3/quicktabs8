<?php
/**
 * @file
 * Contains \Drupal\quicktabs\Plugin\TabType\BlockContent.
 */

namespace Drupal\quicktabs\Plugin\TabType;

use Drupal\quicktabs\TabTypeBase;

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
      //'#options' => quicktabs_get_blocks(),
      '#options' => array('test1' => 'Test 1', 'test2' => 'Test 2'),
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
  //public function optionsForm() {
    //return array();
  //}
}
