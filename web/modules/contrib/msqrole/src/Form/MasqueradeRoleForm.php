<?php

namespace Drupal\msqrole\Form;

use Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException;
use Drupal\Component\Plugin\Exception\PluginNotFoundException;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\msqrole\RoleManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class MasqueradeRoleForm
 *
 * @package Drupal\msqrole\Form
 */
class MasqueradeRoleForm extends FormBase implements ContainerInjectionInterface {

  /**
   * The role manager.
   *
   * @var \Drupal\msqrole\RoleManagerInterface
   */
  protected $roleManager;

  /**
   * MasqueradeRoleForm constructor.
   *
   * @param \Drupal\msqrole\RoleManagerInterface $role_manager
   *   The role manager.
   */
  public function __construct(RoleManagerInterface $role_manager) {
    $this->roleManager = $role_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('msqrole.manager')
    );
  }

  /**
   * Returns a unique string identifying the form.
   *
   * @return string
   *   The unique string identifying the form.
   */
  public function getFormId() {
    return 'msqrole_form';
  }

  /**
   * Form constructor.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return array
   *   The form structure.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $roles = $this->roleManager->getConfigurableRoles();
    $user_roles = [];
    $check_roles = $this->currentUser()->getRoles();

    /**
     * @var string $id
     *   The role ID.
     * @var \Drupal\user\RoleInterface $role
     *   The role object.
     */
    foreach ($roles as $id => &$role) {
      if (!$this->currentUser()->hasPermission('masquerade as ' . $role->id())
        || $this->currentUser()->id() === 1) {
        unset($roles[$id]);
        continue;
      }

      $role = $role->label();
      if (in_array($id, $check_roles)) {
        $user_roles[] = $id;
      }
    }

    $form['description'] = [
      '#type' => 'markup',
      '#markup' => 'To view website as the anonymous role, open an incognito window or log-out.<br />
The authenticated role is always automatically selected.',
    ];

    $form['roles'] = [
      '#type' => 'select',
      '#multiple' => TRUE,
      '#title' => $this->t('Roles'),
      '#description' => $this->t('Select the roles you wish to masquerade as.'),
      '#required' => TRUE,
      '#options' => $roles,
      '#default_value' => $user_roles,
    ];

    $form['actions'] = [
      '#type' => 'actions',
      'submit' => [
        '#type' => 'submit',
        '#value' => $this->roleManager->isActive($this->currentUser()->id())
          ? $this->t('Reset')
          : $this->t('Submit'),
      ],
    ];

    return $form;

  }

  /**
   * Validate the form.
   *
   * @param array $form
   *   The form structure.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    $roles = $form_state->getValue('roles');
    if (count($roles) === 0) {
      $form_state->setErrorByName('roles', $this->t('You must select at least one role to masquerade as!'));
    } elseif (in_array('administrator', $roles)) {
      if (!$this->currentUser()->hasPermission('masquerade as administrator')) {
        $form_state->setErrorByName('roles', $this->t('Your current role is not allowed to masquerade as an administrator.'));
      }
    }
  }

  /**
   * Submits role form.
   *
   * @param array $form
   *   The form structure.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    if (!$this->roleManager->isActive($this->currentUser()->id())) {
      $roles = $form_state->getValue('roles');
      if (!isset($roles['authenticated'])) {
        $roles = ['authenticated' => 'authenticated'] + $roles;
      }

      $this->roleManager->setActive($this->currentUser()->id(), TRUE);
      $this->roleManager->setRoles($this->currentUser()->id(), $roles);
    } else {
      $this->roleManager->removeData($this->currentUser()->id());
    }

    // Invalidate cache tags.
    try {
      $this->roleManager->invalidateTags($this->currentUser()->id());
    } catch (InvalidPluginDefinitionException | PluginNotFoundException $e) {
    }

    $form_state->setRedirect('<front>');
  }

}
