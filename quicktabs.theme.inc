<?php

use Drupal\Core\Template\Attribute;

/**
 * @file
 * Theme for quicktabs views.
 */

/**
 * Prepares variables for quicktabs view template.
 *
 * Default template: quicktabs-view-quicktabs.html.twig.
 *
 * Create a tabbed view
 *
 * @param array $variables
 *   An associative array
 */
function template_preprocess_quicktabs_view_quicktabs(&$variables) {
  $view = $variables['view'];
  $rows = $variables['rows'];
  $style = $view->style_plugin;
  $options = $style->options;

  // TODO: do we still need these?
  $variables['default_row_class'] = !empty($options['default_row_class']);
  $variables['total_rows'] = $view->total_rows;

  $quicktab_id = str_replace('_', '-', $view->id());
  $variables['quicktab_id'] = $quicktab_id;
  $variables['#attached']['drupalSettings']['quicktabs']['qt_' . $quicktab_id] = new stdClass();
  $variables['#attached']['drupalSettings']['quicktabs']['qt_' . $quicktab_id]->tabs = [];
  foreach ($rows as $id => $row) {
    $entity = $row['#row']->_entity;
    $variables['rows'][$id] = array();
    $variables['rows'][$id]['content'] = $row;

    $attributes = [];
    $quicktab_classes = [];
    if ($options['default_row_class']) {
      $quicktab_classes[] = 'views-row';
    }
    if (!empty($options['row_class'])) {
      $quicktab_classes[] = $options['row_class'];
    }

    $attributes['class'] = $quicktab_classes;
    $variables['rows'][$id]['content']['#theme_wrappers'] = [
      'container' => [
        '#attributes' => $attributes,
      ],
    ];
  }

  $set_mapping = $style->getSetMapping();
  if (!empty($style->getTabs())) {
    $current_set = 0;
    $rows_with_page_starts = [];
    $rows_with_page_endings = [];
    foreach ($set_mapping as $index => $row_numbers) {
      $rows_with_page_starts[] = reset($row_numbers);
      $rows_with_page_endings[] = array_pop($row_numbers);

      $object = new stdClass();
      $object->tab_page = $current_set;
      $object->title = $index;
      $variables['#attached']['drupalSettings']['quicktabs']['qt_' . $quicktab_id]->tabs[] = $object;
      $current_set++;
    }

    unset($variables['title']);

    $rows_with_tabs = [0];
    $variables['tabs'] = $style->getTabs();
    $variables['rows_with_tabs'] = $rows_with_tabs;
    $variables['rows_with_page_starts'] = $rows_with_page_starts;
    $variables['rows_with_page_endings'] = $rows_with_page_endings;
  }

  $variables['pages_prefix'] = $pages_prefix;
}
