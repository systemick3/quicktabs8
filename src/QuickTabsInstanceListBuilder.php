<?php

namespace Drupal\quicktabs;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;

/**
 * Provides a listing of quicktabs_instances.
 *
 * @todo Would making this sortable help in specifying the importance of a quicktabs instance?
 */
class QuickTabsInstanceListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $row['label'] = $entity->getLabel();
    $row['storage'] = $this->t('Normal');
    return $row + parent::buildRow($entity);
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['label'] = $this->t('Label');
    $header['storage'] = $this->t('Storage');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function getDefaultOperations(EntityInterface $entity) {
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
