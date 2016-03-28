<?php
/**
 * @file
 * Contains \Drupal\quicktabs\Tests\QuicktabsPathTest.php.
 */

namespace Drupal\quicktabs\Tests;


use Drupal\simpletest\WebTestBase;

/**
 * Check all the paths of the module.
 *
 * @group Quicktabs
 */

class QuicktabsPathTest extends WebTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = array('quicktabs');

  /**
   * Test user.
   */
  private $admin_user;

  function setUp() {
    parent::setUp();

    $this->admin_user = $this->drupalCreateUser(array('administer quicktabs', 'access administration pages'));

    $this->drupalLogin($this->admin_user);
  }

  function testQuicktabsPath() {

    $this->drupalLogin($this->admin_user);

    $paths = array('admin/structure/quicktabs',
      'admin/structure/quicktabs/add',
      'admin/structure/quicktabs/list');

    foreach ($paths as $path) {
      $this->drupalGet($path);
      $this->assertResponse(200, '200 response for path: ' . $path);
    }
  }
}