<?php

namespace Drupal\msqrole;

use Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException;
use Drupal\Component\Plugin\Exception\PluginNotFoundException;
use Drupal\Core\Session\AccountProxy;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\user\RoleStorageInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class MasqueradeAccountProxy
 *
 * @package Drupal\msqrole
 */
class MasqueradeAccountProxy extends AccountProxy implements AccountProxyInterface {

  /**
   * The role manager.
   *
   * @var \Drupal\msqrole\RoleManagerInterface
   */
  protected $roleManager;

  /**
   * AccountProxy constructor.
   *
   * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface|null $event_dispatcher
   *   The event dispatcher.
   * @param \Drupal\msqrole\RoleManagerInterface $role_manager
   *   The role manager.
   */
  public function __construct(EventDispatcherInterface $event_dispatcher, RoleManagerInterface $role_manager) {
    parent::__construct($event_dispatcher);
    $this->roleManager = $role_manager;
  }

  /**
   * Returns the role storage object.
   *
   * @return \Drupal\user\RoleStorageInterface
   *   The role storage object.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  protected function getRoleStorage(): RoleStorageInterface {
    return $this->entityTypeManager->getStorage('user_role');
  }

  /**
   * {@inheritdoc}
   */
  public function getRoles($exclude_locked_roles = FALSE) {
    if (!$this->roleManager->isActive($this->getAccount()->id())) {
      return $this->getAccount()->getRoles($exclude_locked_roles);
    }
    return $this->roleManager->getRoles($this->getAccount()->id());
  }

  /**
   * {@inheritdoc}
   */
  public function hasPermission($permission) {
    $default = $this->getAccount()->hasPermission($permission);
    if (!$this->roleManager->isActive($this->getAccount()->id())) {
      return $default;
    }
    try {
      return $this->roleManager
        ->isPermissionInRoles($permission, $this->getRoles());
    } catch (InvalidPluginDefinitionException | PluginNotFoundException $e) {
      return $default;
    }
  }

}
