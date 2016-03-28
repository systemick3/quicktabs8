<?php
/**
 * @file
 * Contains \Drupal\quicktabs\Form\QuicktabDeleteForm.php
 */
namespace Drupal\quicktabs\Form;

use Drupal\Core\Entity\EntityConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Class QuicktabAddForm
 *
 */
class QuicktabDeleteForm extends EntityConfirmFormBase {

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    /**
     * @var \Drupal\quicktabs\Entity\QuickSet $entity
     */
    $entity = $this->entity;
    return $this->t('Are you sure you want to delete this quicktabs instance with name %name?',array('%name' => $entity->getTitle()));
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return new Url('quicktabs.list_tabs');
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return $this->t('Delete');
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state){
    $this->entity->delete();
    $form_state->setRedirectUrl($this->getCancelUrl());
  }
}
