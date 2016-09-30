(function ($, Drupal, drupalSettings) {
  
'use strict';

Drupal.behaviors.qt_accordion = {
  attach: function (context, settings) {
    $(context).find('div.quicktabs-accordion').once('quicktabs-accordion').each(function() {
      var id = $(this).attr('id');
      var qtKey = 'qt_' + this.id.substring(this.id.indexOf('-') +1);
      var options = drupalSettings.quicktabs[qtKey].options;
      //options = {
        //'collapsible': 1,
        //'active': 2
      //}
      if (options.history) {
        //options.event = 'change';
        $(this).accordion(options);
        Drupal.quicktabsBbq($(this), 'h3 a', 'h3');
      }
      else {
        $(this).accordion(options);
      }
      //$(this).accordion(options);
      //$(this).accordion({
        //'collapsible': 0
      //});
     
      //options.active = 1; 
      //options.active = parseInt(drupalSettings.quicktabs[qtKey].active_tab);
      //alert('active = ' . options.active);
      //var el = $(this);
      //Drupal.quicktabs.prepare(el);
    });

    /*$('.quick-accordion', context).once(function(){
      var id = $(this).attr('id');
      var qtKey = 'qt_' + this.id.substring(this.id.indexOf('-') +1);
      var options = settings.quicktabs[qtKey].options;

      options.active = parseInt(settings.quicktabs[qtKey].active_tab);
      if (settings.quicktabs[qtKey].history) {
        options.event = 'change';
        $(this).accordion(options);
        Drupal.quicktabsBbq($(this), 'h3 a', 'h3');
      }
      else {
        $(this).accordion(options);
      }
    });*/
  }
}

})(jQuery, Drupal, drupalSettings);
