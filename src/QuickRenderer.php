<?php

namespace Drupal\quicktabs;


/**
 * Abstract base class for QuickSet Renderers.
 *
 * A renderer object contains a reference to a QuickSet object, which it can
 * then render.
 */
abstract class QuickRenderer {

  /**
   * @var QuickSet
   */
  protected $quickset;

  /**
   * Constructor
   */
  public function __construct($quickset) {
    $this->quickset = $quickset;
  }

  /**
   * Accessor method for the title.
   */
  public function getTitle() {
    return $this->quickset->getTitle();
  }

  /**
   * The only method that renderer plugins must implement.
   *
   * @return array A render array to be passed to drupal_render().
   */
  abstract public function render();


  /**
   * Method for returning the form elements to display for this renderer type on
   * the admin form.

   * @param $qt \Drupal\quicktabs\QuickSet object representing the Quicktabs instance that the tabs are
   * being built for.
   *
   * @return array
   */
  public static function optionsForm($qt) {
    return array();
  }

}