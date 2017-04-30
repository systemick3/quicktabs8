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

  $tab_title_field = $options['tab_title_field'];
  if(empty($tab_title_field)) {
    drupal_set_message(t("No field configured for the tab title."), "error");
    return "";
  }

  $quicktab_id = str_replace('_', '-', $view->id());
  $variables['quicktab_id'] = $quicktab_id;
  $variables['#attached']['drupalSettings']['quicktabs']['qt_' . $quicktab_id] = new stdClass();
  $variables['#attached']['drupalSettings']['quicktabs']['qt_' . $quicktab_id]->tabs = [];

  // TODO: do we still need these?
  $variables['default_row_class'] = !empty($options['default_row_class']);
  $variables['total_rows'] = $view->total_rows;

  foreach ($rows as $id => $row) {
    $entity = $row['#row']->_entity;
    $variables['rows'][$id] = array();
    $variables['rows'][$id]['content'] = $row;

    if (empty($options['grouping'])) {
      $quicktab_classes = ['quicktabs-tabpage'];
      if ($id > 0) {
        $quicktab_classes[] = 'quicktabs-hide';
      }
      $variables['rows'][$id]['content']['#theme_wrappers'] = [
        'container' => [
        '#attributes' => [
          'class' => $quicktab_classes,
            'id' => 'quicktabs-tabpage-' . $quicktab_id . '-' . $id,
          ],
        ],
      ];
      $object = new stdClass();
      $object->tab_page = $id;
      $object->title = $entity->get($tab_title_field)->value;
      $variables['#attached']['drupalSettings']['quicktabs']['qt_' . $quicktab_id]->tabs[] = $object;
    }
  }

  $set_mapping = $style->getSetMapping();
  $set_is_mapped = FALSE;
  $page_prefix = FALSE;
  if (empty($options['grouping'])) {
    $page_prefix = TRUE;
  }
  else if (is_array($set_mapping)) {
    $current_set = 0;
    foreach ($set_mapping as $index => $item) {
      if (empty(array_diff($set_mapping[$index], array_keys($rows)))) {
        $set_is_mapped = TRUE;
        if ($current_set == 0) {
          $page_prefix = TRUE;
        }
        else {
          $page_prefix = FALSE;
        }
        $classes = 'quicktabs-tabpage';

        if ($current_set > 0) {
          $classes .= ' quicktabs-hide';
        }
        $variables['set_prefix']['classes'] = $classes;
        $variables['set_prefix']['id'] = 'quicktabs-tabpage-' . $quicktab_id . '-' . $current_set;
      }

      $object = new stdClass();
      $object->tab_page = $current_set;
      $object->title = $index;
      $variables['#attached']['drupalSettings']['quicktabs']['qt_' . $quicktab_id]->tabs[] = $object;
      $current_set++;
    }
  }

  $variables['page_prefix'] = $page_prefix;
}
