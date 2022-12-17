<?php

namespace Drupal\msqrole;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\user\UserDataInterface;

/**
 * Class RoleManager.
 *
 * @package Drupal\msqrole
 */
class RoleManager implements RoleManagerInterface {

  /**
   * The user data instance.
   *
   * @var \Drupal\user\UserDataInterface
   */
  protected $userData;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  protected $config;

  /**
   * RoleManager constructor.
   *
   * @param \Drupal\user\UserDataInterface $user_data
   *   The user data object.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   */
  public function __construct(UserDataInterface $user_data, EntityTypeManagerInterface $entity_type_manager, ConfigFactoryInterface $config_factory) {
    $this->userData = $user_data;
    $this->entityTypeManager = $entity_type_manager;
    $this->config = $config_factory->get('msqrole.settings');
  }

  /**
   * {@inheritDoc}
   */
  public function isPermissionInRoles($permission, array $roles) {
    $roles = $this->getAllRoles($roles);
    if (!$roles) {
      return FALSE;
    }

    /** @var \Drupal\user\RoleInterface $role */
    foreach ($roles as $role) {
      if ($role->hasPermission($permission)) {
        return TRUE;
      }
    }

    return FALSE;
  }

  /**
   * {@inheritDoc}
   */
  public function getAllRoles(?array $rids = NULL) {
    if (is_array($rids)) {
      return $this->entityTypeManager
        ->getStorage('user_role')
        ->loadMultiple($rids);
    }
    return $this->entityTypeManager
      ->getStorage('user_role')
      ->loadMultiple();
  }

  /**
   * {@inheritDoc}
   */
  public function getConfigurableRoles() {
    /** @var RoleManagerInterface $role_manager */
    $roles = $this->getAllRoles();
    $disallow_roles = [
      'anonymous',
      'authenticated',
      'administrator',
    ];

    // Unset roles that shouldn't be masqueraded as.
    foreach ($disallow_roles as $role) {
      if (!isset($roles[$role])) {
        continue;
      }
      unset($roles[$role]);
    }

    return $roles;
  }

  /**
   * {@inheritDoc}
   */
  public function getRoles($uid) {
    $data = [];
    if (!empty($this->getData($uid, 'roles'))) {
      $data = unserialize($this->getData($uid, 'roles'));
    }
    return $data;
  }

  /**
   * {@inheritDoc}
   */
  public function getData($uid, string $key) {
    return $this->userData->get('msqrole', $uid, $key);
  }

  /**
   * {@inheritDoc}
   */
  public function setRoles($uid, array $roles) {
    return $this->setData($uid, 'roles', serialize($roles));
  }

  /**
   * {@inheritDoc}
   */
  public function setData($uid, string $key, $value) {
    return $this->userData->set('msqrole', $uid, $key, $value);
  }

  /**
   * {@inheritDoc}
   */
  public function isActive($uid) {
    return $this->getData($uid, 'active') ?? FALSE;
  }

  /**
   * {@inheritDoc}
   */
  public function setActive($uid, bool $active) {
    return $this->setData($uid, 'active', $active ? 1 : 0);
  }

  /**
   * {@inheritDoc}
   */
  public function removeData($uid, ?string $key = NULL) {
    return $this->userData->delete('msqrole', $uid, $key);
  }

  /**
   * {@inheritDoc}
   */
  public function invalidateTags($uid) {
    $custom_tags = unserialize($this->config->get('tags_to_invalidate')) ?: [];
    // Replace possible variables.
    // @todo use tokens?
    foreach ($custom_tags as &$tag) {
      $tag = str_replace('{current_user.id}', $uid, $tag);
    }
    Cache::invalidateTags(array_merge(RoleManagerInterface::TAGS_TO_INVALIDATE, ['user:' . $uid], $custom_tags));
  }

}
