<?php

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

  $variables['default_row_class'] = !empty($options['default_row_class']);
  foreach ($rows as $id => $row) {
    $entity = $row['#row']->_entity;
    $variables['rows'][$id] = array();
    $variables['rows'][$id]['tab_link'] = $entity->url();
    $variables['rows'][$id]['tab_title'] = $entity->get($tab_title_field)->value;
    $variables['rows'][$id]['content'] = $row;
    $object = new stdClass();
    $object->tab_page = $id;
    $object->title = $entity->get($tab_title_field)->value;
    $variables['#attached']['drupalSettings']['quicktabs']['qt_' . $quicktab_id]->tabs[] = $object;
  }
}
