<?php

/**
 * @file
 * Definition of Drupal\quicktabs\tests\QuickTabsAdminTest.
 */

namespace Drupal\quicktabs\Tests;

use Drupal\simpletest\WebTestBase;
use Drupal\taxonomy\Entity\Vocabulary;
use Drupal\Component\Utility\Unicode;
use Drupal\Core\Language\LanguageInterface;

/**
 * Tests creating and saving a QuickTabs instance..
 *
 * @group quicktabs
 */
class QuickTabsAdminTest extends WebTestBase {

  /**
   * A user with permission to access the administrative toolbar.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $adminUser;

  /**
   * A n array of vocabularies.
   *
   * @var \Drupal\user\UserInterface
   */
  //protected $vocabularies;
  
  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = array('node', 'block', 'menu_ui', 'user', 'taxonomy', 'toolbar', 'quicktabs', 'views');

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $perms = array(
      'access toolbar',
      'access administration pages',
      'administer site configuration',
      'bypass node access',
      'administer themes',
      'administer nodes',
      'access content overview',
      'administer blocks',
      'administer menu',
      'administer modules',
      'administer permissions',
      'administer users',
      'access user profiles',
      'administer taxonomy',
      'administer quicktabs',
    );

    // Create an administrative user and log it in.
    $this->adminUser = $this->drupalCreateUser($perms);

    $this->drupalLogin($this->adminUser);

    //$this->vocabularies = array();
    
    /*$vocabulary1 = entity_create('taxonomy_vocabulary', array(
      'name' => $this->randomMachineName(),
      'description' => $this->randomMachineName(),
      'vid' => Unicode::strtolower($this->randomMachineName()),
      'langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED,
      'weight' => mt_rand(0, 10),
    ));
    $vocabulary1->save();
    $this->vocabularies[] = $vocabulary1;
    
    $vocabulary2 = entity_create('taxonomy_vocabulary', array(
      'name' => $this->randomMachineName(),
      'description' => $this->randomMachineName(),
      'vid' => Unicode::strtolower($this->randomMachineName()),
      'langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED,
      'weight' => mt_rand(0, 10),
    ));
    $vocabulary2->save();
    $this->vocabularies[] = $vocabulary2;*/
  }

  /**
   * Test all vocabularies appear on admin page.
   */
  function testAllVocabulariesLoaded() {
    $this->drupalGet('admin/structure/quicktabs');
    $this->assertResponse(200);
    $this->assertRaw('Quick Tabs');
    $this->drupalGet('admin/structure/quicktabs/add');
    $this->assertResponse(200);
    $this->assertRaw('Add Quicktabs Instance');
    //$this->assertRaw('Each Quicktabs instance has a corresponding block');

    //foreach($this->vocabularies as $vocabulary) {
      //$this->assertRaw($vocabulary->get('name'));
    //}
  }
}
