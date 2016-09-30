(function ($, Drupal, drupalSettings) {

Drupal.behaviors.qt_ui_tabs = {
  attach: function (context) {

    $(context).find('div.quicktabs-ui-wrapper').once('quicktabs-ui-wrapper').each(function() {
      var id = $(this).attr('id');
      var qtKey = 'qt_' + id.substring(id.indexOf('-') +1);
      if (drupalSettings.quicktabs[qtKey].history == 1) {
        $(this).tabs({event: 'change'});
        Drupal.quicktabsBbq($(this), 'ul.ui-tabs-nav a');
      }
      else {
        $(this).tabs();
      }
    });
  }
}

})(jQuery, Drupal, drupalSettings);
