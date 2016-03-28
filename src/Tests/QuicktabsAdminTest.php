<?php
/**
 * @file
 * Contains \Drupal\quicktabs\Tests\QuicktabsAdminTest.php.
 */

namespace Drupal\quicktabs\Tests;

use Drupal\Core\Language\Language;
use Drupal\Node\Entity\Node;
use Drupal\simpletest\WebTestBase;
use Drupal\Core\Entity\Entity;

/**
 * Add, edit and delete quicktabs.
 *
 * @group Quicktabs
 */
class QuicktabsAdminTest extends WebTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = array('quicktabs', 'node');


  /**
   * Test user.
   */
  private $admin_user;

  function setUp() {
    parent::setUp();

    $this->admin_user = $this->drupalCreateUser(array(
      'administer quicktabs',
      'administer nodes'
    ));
    $this->drupalLogin($this->admin_user);

    for ($i = 0; $i < 5; $i++) {
      $node = array();
      $node['type'] = 'page';
      $node['uid'] = $i;
      $node['title'] = 'This is node number ' . ($i + 1);
      $node['body'][Language::LANGCODE_NOT_SPECIFIED][0]['value'] = $this->randomString(255);
      $loader = Node::create($node);
      $loader->save();
    }
  }

  /**
   * Create a Quicktabs instance through the UI and ensure that it is saved properly.
   */
  function testQuicktabsAdmin() {
    $this->drupalLogin($this->admin_user);


    $id = strtolower($this->randomMachineName());
    $title = $this->randomMachineName();
    $renderer = 'quicktabs';
    $style = 'nostyle';
    $ajax = FALSE;
    $hide_empty_tabs = 1;


    $config = \Drupal::service('config.factory')
          ->getEditable('quicktabs.settings')
          ->set('id',$id)
          ->set('title',$title)
          ->set('renderer',$renderer)
          ->set('style',$style)
          ->set('ajax',$ajax)
          ->set('hide_empty_tabs',$hide_empty_tabs)->save();
    $id_result = \Drupal::service('config.factory')->get('quicktabs.settings')->get('id');
    $title_result = \Drupal::service('config.factory')->get('quicktabs.settings')->get('title');
    $renderer_result = \Drupal::service('config.factory')->get('quicktabs.settings')->get('renderer');
    $style_result = \Drupal::service('config.factory')->get('quicktabs.settings')->get('style');
    $ajax_result = \Drupal::service('config.factory')->get('quicktabs.settings')->get('ajax');
    $hide_empty_tabs_result = \Drupal::service('config.factory')->get('quicktabs.settings')->get('hide_empty_tabs');


    $this->assertEqual($id, $id_result);

    $this->assertEqual($title, $title_result);

    $this->assertEqual($renderer, $renderer_result);

    $this->assertEqual($style, $style_result);

    $this->assertEqual($ajax, $ajax_result);

    $this->assertEqual($hide_empty_tabs, $hide_empty_tabs_result);
  }
}

