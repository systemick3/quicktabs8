<?php
/**
 * @file
 * Contains \Drupal\quicktabs\QuicktabListBuilder.
 */

namespace Drupal\quicktabs;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Config\Entity\ConfigEntityListBuilder;

/**
 * Class QuicktabListBuilder
 *
 * @see \Drupal\quicktabs\Entity\QuickSet
 */
class QuicktabListBuilder extends ConfigEntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'quicktabs_list';
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['title'] = $this->t('QuickSet');
    $header['storage'] = $this->t('Storage');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $row['title'] = $entity->id();
    $row['storage'] = $this->t('Normal');
    return $row + parent::buildRow($entity);
  }

  /**
   * {@inheritdoc}
   */
  public function getDefaultOperations(EntityInterface $entity) {
    /** @var \Drupal\Core\Config\Entity\ConfigEntityInterface $entity */
    $operations = parent::getDefaultOperations($entity);

    if ($entity->hasLinkTemplate('edit')) {
      $operations['edit'] = array(
        'title' => t('Edit quicktab'),
        'weight' => 10,
        'url' => $entity->urlInfo('edit'),
      );
      $operations['delete'] = array(
        'title' => t('Delete quicktab'),
        'weight' => 20,
        'url' => $entity->urlInfo('delete'),
      );
      $operations['clone'] = array(
        'title' => t('Clone quicktab'),
        'weight' => 30,
        'url' => $entity->urlInfo('clone'),
      );
      $operations['export'] = array(
        'title' => t('Export quicktab'),
        'weight' => 40,
        'url' => $entity->urlInfo('export'),
      );
    }
    return $operations;
  }
}
