(function ($, Drupal, drupalSettings) {

Drupal.behaviors.qt_ui_tabs = {
  attach: function (context, settings) {

    $(context).find('div.quicktabs-ui-wrapper').once('quicktabs-ui-wrapper').each(function() {
      var id = $(this).attr('id');
      var qtKey = 'qt_' + id.substring(id.indexOf('-') +1);
      $(this).tabs();

    });

    /*$('.quicktabs-ui-wrapper').once('qt-ui-tabs-processed', function() {
      var id = $(this).attr('id');
      var qtKey = 'qt_' + id.substring(id.indexOf('-') +1);
      if (!settings.quicktabs[qtKey].history) {
        $(this).tabs();
      }
      else {
        $(this).tabs({event: 'change'});
        Drupal.quicktabsBbq($(this), 'ul.ui-tabs-nav a');
      }
    });*/

  }
}

})(jQuery, Drupal, drupalSettings);
