<?php

namespace Drupal\tooltip_taxonomy\Form;

use Drupal\Core\Entity\EntityConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Builds the form to delete an Example.
 *
 * @author Mingsong Hu
 */
class FilterConditionDeleteForm extends EntityConfirmFormBase {

  /**
   * Confirmation question before delete.
   *
   * @see \Drupal\Core\Form\ConfirmFormInterface::getQuestion()
   */
  public function getQuestion() {
    return $this->t('Are you sure you want to delete %name ?', ['%name' => $this->entity->label()]);
  }

  /**
   * {@inheritdoc}
   *
   * @see \Drupal\Core\Form\ConfirmFormInterface::getCancelUrl()
   */
  public function getCancelUrl() {
    return new Url('entity.tooltip_taxonomy.config');
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
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Invalidate the cache tag for this condition.
    \Drupal::service('cache_tags.invalidator')->invalidateTags(['tooltip_taxonomy:' . $this->entity->id()]);

    $this->entity->delete();
    $this->messenger()->addMessage($this->t('%label has been deleted.', ['%label' => $this->entity->label()]));

    $form_state->setRedirectUrl($this->getCancelUrl());
  }

}
