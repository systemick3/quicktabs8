<?php

namespace Drupal\quicktabs\Plugin\views\style;

use Drupal\core\form\FormStateInterface;
use Drupal\views\Plugin\views\style\StylePluginBase;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\Core\StringTranslation\TranslatableMarkup;

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
  //protected $usesRowClass = TRUE;

  /**
   * Should field labels be enabled by default.
   *
   * @var bool
   */
  protected $defaultFieldLabels = TRUE;

  /**
   * Should field labels be enabled by default.
   *
   * @var bool
   */
  protected $setMapping;
  
  /**
   * Set default options
   */
  protected function defineOptions() {
    $options = parent::defineOptions();
    $options['path'] = array('default' => 'quicktabs');
    return $options;
  }

  /**
   * Set the set mapping.
   */
  public function setSetMapping(array $setMapping) {
    $this->setMapping = $setMapping;
  }

  /**
   * Get the set mapping.
   */
  public function getSetMapping() {
    return $this->setMapping;
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

  /**
   * {@inheritdoc}
   */
  public function renderGroupingSets($sets, $level = 0) {
    $output = array();
    $theme_functions = $this->view->buildThemeFunctions($this->groupingTheme);
    $tab_titles = [];
    $link_classes = ['loaded'];
    $quicktab_id = str_replace('_', '-', $this->view->id());
    foreach ($sets as $index => $set) {
      // Create the links for the tabs
      if (!empty($this->options['grouping'])) {
        // Grouping applied - use the array key
        $tab_titles[] = [
          '0' => Link::fromTextAndUrl(
            new TranslatableMarkup($index),
            Url::fromRoute(
              '<current>',
              [],
              [
                'attributes' => array(
                  'class' => $link_classes,
                ),
              ]
            )
          )->toRenderable(),
        ];
      }
      else {
        // no grouping - get the field value from the entity atttached to each row
        $tab_title_field = $this->options['tab_title_field'];
        foreach($set['rows'] as $row) {
          $entity = $row->_entity;
          $tab_titles[] = [
            '0' => Link::fromTextAndUrl(
              new TranslatableMarkup($entity->get($tab_title_field)->value),
              Url::fromRoute(
                '<current>',
                [],
                [
                  'attributes' => [
                    'class' => $link_classes,
                  ],
                ]
              )
            )->toRenderable(),
          ];
        }
      } // End of create tab links code

      $level = isset($set['level']) ? $set['level'] : 0;

      $row = reset($set['rows']);
      // Render as a grouping set.
      if (is_array($row) && isset($row['group'])) {
        $single_output = array(
          '#theme' => $theme_functions,
          '#view' => $this->view,
          '#grouping' => $this->options['grouping'][$level],
          '#rows' => $set['rows'],
        );
      }
      // Render as a record set.
      else {
        if ($this->usesRowPlugin()) {
          foreach ($set['rows'] as $index => $row) {
            $this->view->row_index = $index;
            $set['rows'][$index] = $this->view->rowPlugin->render($row);
          }
        }

        $single_output = $this->renderRowGroup($set['rows']);
      }

      $single_output['#grouping_level'] = $level;
      $single_output['#title'] = $set['group'];

      // Create a mapping of which rows belong in which set
      // This can then be used in the theme function to wrap each tab page
      if (!empty($this->options['grouping'])) {
        $set_mapping = [];
        foreach($sets as $set_index => $set) {
          foreach ($set['rows'] as $row_index => $row) {
            $set_mapping[$set_index][] = $row_index;
          }
        }
        $this->setSetMapping($set_mapping);
      }

      $output[] = $single_output;
    }
    unset($this->view->row_index);

    // Create the tabs for rendering
    $tabs = [
      '#theme' => 'item_list',
      '#items' => $tab_titles,
      '#attributes' => [
        'class' => ['quicktabs-tabs'],
      ],
    ];

    // Add tabs to the build
    array_unshift($output, $tabs);

    // Add quicktabs wrapper to all the output
    $output['#theme_wrappers'] = [
      'container' => [
        '#attributes' => [
          'class' => ['quicktabs-wrapper'],
          'id' => 'quicktabs-' . $quicktab_id,
        ],
      ],
    ];

    return $output;
  }
}
