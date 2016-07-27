<?php
/**
 * @file
 * Contains \Drupal\quicktabs\Form\QuickTabsInstanceEditForm.php
 */

namespace Drupal\quicktabs\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;
use Drupal\Core\Url;
use Drupal\Core\Entity\EntityForm;

/**
 * Class QuickTabsInstanceEditForm
 *
 */
class QuickTabsInstanceEditForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'quicktab_instance_edit';
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $renderer_options = array('accordian', 'quicktabs', 'ui_tabs');
    
    $form = parent::form($form, $form_state);

    $entity = $this->entity;

    if (empty($this->entity->tabs)) {
      $qt = new \stdClass;
      $qt->tabs = array(
        0 => array(),
        1 => array(),
      );
    }

    $form['label'] = array(
      '#title' => $this->t('Name'),
      '#description' => $this->t('This will appear as the block title.'),
      '#type' => 'textfield',
      '#default_value' => $this->entity->label(),
      '#weight' => -9,
      '#required' => TRUE,
      '#placeholder' => $this->t('Enter name'),
    );

    $form['id'] = array(
      '#type' => 'machine_name',
      '#maxlength' => 32,
      '#required' => TRUE,
      '#default_value' => $this->entity->id(),
      '#machine_name' => array(
        'exists' => 'Drupal\quicktabs\Entity\QuicktabsInstance::getQuicktabsInstance',
      ),
      '#description' => $this->t('A unique machine-readable name for this Quicktabs instance. It must only contain lowercase letters, numbers, and underscores. The machine name will be used internally by Quicktabs and will be used in the CSS ID of your Quicktabs block.'),
      '#weight' => -8,
    );

    $form['renderer'] = array(
      '#type' => 'select',
      '#title' => $this->t('Renderer'),
      '#options' => array(
        'accordian',
        'quicktabs',
        'ui_tabs'
      ),
      '#default_value' => $this->entity->getRenderer(),
      '#description' => $this->t('Choose how to render the content.'),
      '#weight' => -7,
    );

    $form['ajax'] = array(
      '#type' => 'radios',
      '#title' => t('Ajax'),
      '#options' => array(
        TRUE => $this->t('Yes') . ': ' . t('Load only the first tab on page view'),
        FALSE => $this->t('No') . ': ' . t('Load all tabs on page view.'),
      ),
      '#default_value' => $this->entity->isAjax(),
      '#description' => $this->t('Choose how the content of tabs should be loaded.<p>By choosing "Yes", only the first tab will be loaded when the page first viewed. Content for other tabs will be loaded only when the user clicks the other tab. This will provide faster initial page loading, but subsequent tab clicks will be slower. This can place less load on a server.</p><p>By choosing "No", all tabs will be loaded when the page is first viewed. This will provide slower initial page loading, and more server load, but subsequent tab clicks will be faster for the user. Use with care if you have heavy views.</p><p>Warning: if you enable Ajax, any block you add to this quicktabs block will be accessible to anonymous users, even if you place role restrictions on the quicktabs block. Do not enable Ajax if the quicktabs block includes any blocks with potentially sensitive information.</p>'),
      //'#states' => array('visible' => array(':input[name="renderer"]' => array('value' => 'quicktabs'))),
      '#weight' => -6,
    );

    $form['style'] = array(
      '#type' => 'select',
      '#title' => $this->t('Style'),
      '#options' => array('none', 'option1', 'option2',),
      '#weight' => -5,
      '#default_value' => $this->entity->getStyle(),
      '#description' => $this->t('<p>Yet to be implemented</p>'),
    );

    $form['hide_empty_tabs'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Hide empty tabs'),
      '#default_value' => $this->entity->getHideEmptyTabs(),
      '#description' => $this->t('Empty and restricted tabs will not be displayed. Could be useful when the tab content is not accessible.<br />This option does not work in ajax mode.'),
      '#weight' => -4,
    );

    $form['mytable'] = array(
      '#type' => 'table',
      '#header' => array(
         t('Tab title'),
         t('Tab weight'),
         t('Tab type'),
         t('Tab content'),
         t('Operations'),
      ),
      '#empty' => t('There are no tabs yet'),
      //'#tableselect' => TRUE,
      '#tabledrag' => array(
        array(
          'action' => 'order',
          'relationship' => 'sibling',
          'group' => 'mytable-order-weight',
        ),
      ),
    );

    $type = \Drupal::service('plugin.manager.tab_type');
    $plugin_definitions = $type->getDefinitions();

    $types = array();
    foreach ($plugin_definitions as $index => $def) {
      $name = $def['name'];
      $types[$name->render()] = $name->render();
    }

    ksort($types);
    for ($i=0; $i<count($qt->tabs); $i++) {
      // TableDrag: Mark the table row as draggable.
      $form['mytable'][$i]['#attributes']['class'][] = 'draggable';
      // TableDrag: Sort the table row according to its existing/configured weight.
      $form['mytable'][$i]['#weight'] = isset($tab['weight']) ? $tab['weight'] : 0;
      
      $form['mytable'][$i]['title'] = array(
        '#type' => 'textfield',
        '#size' => '10',
        '#default_value' => isset($tab['title']) ? $tab['title'] : '',
      );

      // TableDrag: Weight column element.
      $form['mytable'][$i]['weight'] = array(
        '#type' => 'weight',
        //'#title' => t('Weight for @title', array('@title' => $entity->label())),
        '#title' => t('Weight'),
        '#title_display' => 'invisible',
        '#default_value' =>  isset($tab['weight']) ? $tab['weight'] : 0,
        // Classify the weight element for #tabledrag.
        '#attributes' => array('class' => array('mytable-order-weight')),
      );
      
      $form['mytable'][$i]['type'] = array(
        '#type' => 'radios',
        '#options' => $types,
        '#default_value' => isset($tab['type']) ? $tab['type'] : key($tabtypes),
      );
      
      foreach ($plugin_definitions as $index => $def) {
        $name = $def['name'];
        $form['mytable'][$i]['content'][$name->render()] = array(
          '#prefix' => '<div class="' . $name . '-plugin-content plugin-content">',
          '#suffix' =>'</div>',
        );
        $object = $type->createInstance($index);
        $form['mytable'][$i]['content'][$name->render()]['options'] = $object->optionsForm();
      }

      $form['mytable'][$i]['operations'] = array(
        '#type' => 'submit',
        '#prefix' => '<div>',
        '#suffix' => '<label for="edit-remove">' . t('Delete') . '</label></div>',
        '#value' => 'remove_' . $delta,
        '#attributes' => array('class' => array('delete-tab'), 'title' => t('Click here to delete this tab.')),
        '#submit' => array('quicktabs_remove_tab_submit'),
        '#ajax' => array(
          'callback' => 'quicktabs_ajax_callback',
          'wrapper' => 'quicktab-tabs',
          'method' => 'replace',
          'effect' => 'fade',
        ),
        '#limit_validation_errors' => array(),
      );
    }

    /**
    $form['qt_wrapper'] = array(
      '#tree' => FALSE,
      '#weight' => -3,
      '#prefix' => '<div class="clear-block" id="quicktabs-tabs-wrapper">',
      '#suffix' => '</div>',
    );

    $form['qt_wrapper']['tabs'] = array(
      '#type' => 'table',
      '#header' => array(
        t('Tab title'),
        t('Tab weight'),
        t('Tab type'),
        t('Tab content'),
        t('Operations'),
      ),
      '#tabledrag' => array(
        array(
          'action' => 'order',
          'relationship' => 'sibling',
          'group' => 'qt-tabs-weight',
        ),
      ),
      '#tree' => TRUE,
      '#prefix' => '<div id="quicktab-tabs">',
      '#suffix' => '</div>',
      //'#theme' => 'quicktabs_admin_form_tabs',
    );

    $type = \Drupal::service('plugin.manager.tab_type');
    $plugin_definitions = $type->getDefinitions();

    $types = array();
    foreach ($plugin_definitions as $index => $def) {
      $name = $def['name'];
      $types[$name->render()] = $name->render();
    }

    ksort($types);
    for ($i=0; $i<count($qt->tabs); $i++) {
      $form['qt_wrapper']['tabs'][$i]['title'] = array(
        '#type' => 'textfield',
        '#size' => '10',
        '#default_value' => isset($tab['title']) ? $tab['title'] : '',
      );
      $form['qt_wrapper']['tabs'][$i]['weight'] = array(
        '#type' => 'weight',
        '#default_value' => isset($tab['weight']) ? $tab['weight'] : $delta-100,
        '#delta' => 100,
      );
      $form['qt_wrapper']['tabs'][$i]['type'] = array(
        '#type' => 'radios',
        '#options' => $types,
        '#default_value' => isset($tab['type']) ? $tab['type'] : key($tabtypes),
      );
      $form['qt_wrapper']['tabs'][$i]['content'] = array(
        '#prefix' => '<div class="types-wrapper">',
        '#suffix' => '</div>'
      );
      
      foreach ($plugin_definitions as $index => $def) {
        $name = $def['name'];
        $form['qt_wrapper']['tabs'][$i]['content'][$name->render()] = array(
          '#prefix' => '<div class="' . $name . '-plugin-content plugin-content">',
          '#suffix' =>'</div>',
        );
        $object = $type->createInstance($index);
        $form['qt_wrapper']['tabs'][$i]['content'][$name->render()]['options'] = $object->optionsForm();
      }
 
      $form['qt_wrapper']['tabs'][$i]['operations'] = array(
        '#type' => 'submit',
        '#prefix' => '<div>',
        '#suffix' => '<label for="edit-remove">' . t('Delete') . '</label></div>',
        '#value' => 'remove_' . $delta,
        '#attributes' => array('class' => array('delete-tab'), 'title' => t('Click here to delete this tab.')),
        '#submit' => array('quicktabs_remove_tab_submit'),
        '#ajax' => array(
          'callback' => 'quicktabs_ajax_callback',
          'wrapper' => 'quicktab-tabs',
          'method' => 'replace',
          'effect' => 'fade',
        ),
        '#limit_validation_errors' => array(),
      );
    }

    $form['qt_wrapper']['tabs']['operations'] = array(
      '#type' => 'operations',
      '#links' => array()
    );
    $form['qt_wrapper']['tabs']['operations']['#links']['edit'] = array(
      'title' => $this->t('Edit'),
      'url' => Url::fromRoute('quicktabs.add'),
    );

    */

    $form['qt_wrapper']['tabs_more'] = array(
      '#type' => 'submit',
      '#prefix' => '<div id="add-more-tabs-button">',
      '#suffix' => '<label for="edit-tabs-more">' . t('Add tab') . '</label></div>',
      '#value' => t('More tabs'),
      '#attributes' => array(
        'class' => array('add-tab'),
        'title' => t('Click here to add more tabs.')
      ),
      '#weight' => 1,
      '#submit' => array('quicktabs_more_tabs_submit'),
      '#ajax' => array(
        'callback' => 'quicktabs_ajax_callback',
        'wrapper' => 'quicktab-tabs',
        'effect' => 'fade',
      ),
      '#limit_validation_errors' => array(),
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validate(array $form, FormStateInterface $form_state) {
    $id = $form_state->getValue('id');

    if (empty($id)) {
      $form_state->setErrorByName('machine_name', t('The quicktabs machine name is required.'));
    }
    elseif (!preg_match('!^[a-z0-9_]+$!', $id)) {
      $form_state->setErrorByName('machine_name', t('The quicktabs machine name must contain only lowercase letters, numbers, and underscores.'));
    }

    $tabs = $form_state->getValue('tabs');
    if (!isset($tabs)) {
      $form_state->setErrorByName('', t('At least one tab should be created.'));
    }
    else {
      foreach ($tabs as $j => $tab) {
        if (empty($tab['title'])) {
          $form_state->setErrorByName('tabs][' . $j . '][title', t('Title is required for each tab.'));
        }
      }
    }
  }
  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $status = $this->entity->save();
    if($status==SAVED_NEW) {
      $form_state->setRedirect('quicktabs.admin');
    }
    drupal_set_message($this->t('Your changes have been saved.'));
  }
}
