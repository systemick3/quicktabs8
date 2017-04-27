<?php

namespace Drupal\quicktabs\Plugin\views\style;

use Drupal\core\form\FormStateInterface;
use Drupal\views\Plugin\views\style\StylePluginBase;

/**
 * Style plugin to render views rows as tabs.
 *
 * @ingroup views_style_plugins
 *
 * @ViewsStyle(
 *   id = "quicktabs",
 *   title = @Translation("Quick Tabs"),
 *   help = @Translation("Render each views row as a tab."),
 *   theme = "quicktabs_view_quicktabs",
 *   display_types = { "normal" }
 * )
 *
 */
class Quicktabs extends StylePluginBase {

  /**
   * Does the style plugin allows to use style plugins.
   *
   * @var bool
   */
  protected $usesRowPlugin = TRUE;

  /**
   * Does the style plugin support custom css class for the rows.
   *
   * @var bool
   */
  protected $usesRowClass = TRUE;

  /**
   * Should field labels be enabled by default.
   *
   * @var bool
   */
  protected $defaultFieldLabels = TRUE;
  
  /**
   * Set default options
   */
  protected function defineOptions() {
    $options = parent::defineOptions();
    $options['path'] = array('default' => 'quicktabs');
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    parent::buildOptionsForm($form, $form_state);

    $options = array('' => $this->t('- None -'));
    $field_labels = $this->displayHandler->getFieldLabels(TRUE);
    $options += $field_labels;

    $handlers = $this->displayHandler->getHandlers('field');
    if (empty($handlers)) {
      $form['error_markup'] = array(
        '#markup' => '<div class="messages messages--error">' . $this->t('The Quicktabs display style requires that a field be configured to be used as the tab title.') . '</div>',
      );
      return;
    }
    
    $form['tab_title_field'] = array(
      '#type' => 'select',
      '#title' => $this->t('Title field'),
      '#options' => $options,
      '#required' => TRUE,
      '#default_value' => $this->options['tab_title_field'],
      '#description' => t('Select the field that will be used as the tab title.'),
      '#weight' => -3,
    );
  }
}
