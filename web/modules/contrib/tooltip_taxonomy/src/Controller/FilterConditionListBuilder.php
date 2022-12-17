<?php

namespace Drupal\tooltip_taxonomy\Controller;

use Drupal\Core\Config\Entity\DraggableListBuilder;
use Drupal\Core\Entity\EntityInterface;

/**
 * Provide a listing of Filter Conditions.
 * @author Mingsong Hu
 */
class FilterConditionListBuilder extends DraggableListBuilder {

  /**
   * The key to use for the form element containing the entities.
   * @var string
   */
  protected $entitiesKey = 'conditions';

  /**
   * Get form ID.
   * @see \Drupal\Core\Form\FormInterface::getFormId()
   */
  public function getFormId() {
    return 'tooltip_taxonomy_filter_condition_list';
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header = [];
    $header['label'] = $this->t('Filter conditions');
    // $header['id'] = $this->t('Machine name');.
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $row = [];
    $row['label'] = $entity->label();
    // $row['id'] = $entity->id();
    return $row + parent::buildRow($entity);
  }
}
