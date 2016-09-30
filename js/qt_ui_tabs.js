(function ($, Drupal, drupalSettings) {

'use strict';

Drupal.behaviors.qt_ui_tabs = {
  attach: function (context) {

    $(context).find('div.quicktabs-ui-wrapper').once('quicktabs-ui-wrapper').each(function() {
      $(this).tabs();
    });
  }
}

})(jQuery, Drupal, drupalSettings);
