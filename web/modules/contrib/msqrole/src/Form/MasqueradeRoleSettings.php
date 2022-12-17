<?php

namespace Drupal\msqrole\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\msqrole\RoleManagerInterface;

/**
 * Class MasqueradeRoleSettings
 *
 * @package Drupal\msqrole\Form
 */
class MasqueradeRoleSettings extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'msqrole_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);
    $config = $this->config('msqrole.settings');

    $tags = unserialize($config->get('tags_to_invalidate')) ?: [];

    // Tags.
    $form['tags'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Extra tags to invalidate:'),
      '#default_value' => implode(PHP_EOL, $tags),
      '#description' => $this->t('These tags will be invalidated when changing roles. Each tag gets a new line.'),
    ];

    // Default tags that are always invalidated.
    $form['default_tags'] = [
      '#type' => 'inline_template',
      '#template' => '<strong>Default/always enabled tags:</strong><pre>{{ tags }}</pre>',
      '#context' => [
        'tags' => print_r(implode(PHP_EOL,
          array_merge(RoleManagerInterface::TAGS_TO_INVALIDATE, ['user:{current_user.id}'])), TRUE)
      ],
    ];


    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // Do nothing.
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('msqrole.settings');
    $tags = explode(PHP_EOL, $form_state->getValue('tags'));
    foreach ($tags as &$tag) {
      $tag = trim($tag);
    }
    $config->set('tags_to_invalidate', serialize($tags ?? []));
    $config->save();
    return parent::submitForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'msqrole.settings',
    ];
  }

}

