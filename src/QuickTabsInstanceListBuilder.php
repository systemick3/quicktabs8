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
    $row['label'] = $entity->label();
    $row['id'] = $entity->id();
    $row['storage'] = $entity->getStorage();
    $row['operations']['data'] = $this->buildOperations($entity);
    return $row;
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $row['label'] = $this->t('Label');
    $row['id'] = $this->t('Machine name');
    $row['storage'] = $this->t('Storage');
    $row['operations'] = $this->t('Operations');
    return $row;
  }

}
