<?php

namespace Drupal\msqrole;

/**
 * Interface RoleManagerInterface.
 *
 * @package Drupal\msqrole
 */
interface RoleManagerInterface {

  /**
   * Tags to invalidate when masquerading as different role.
   */
  const TAGS_TO_INVALIDATE = [
    'config:block.block.bartik_local_actions',
    'config:block.block.bartik_local_tasks',
    'config:system.menu.account',
    'config:system.menu.admin',
    'config:system.menu.tools',
    'local_task',
  ];

  /**
   * Checks whether permission is in given roles.
   *
   * @param $permission
   *   The permission to be checked.
   * @param array $roles
   *   The roles to search the permission in.
   *
   * @return bool
   *   Whether given roles contain permission.
   */
  public function isPermissionInRoles($permission, array $roles);

  /**
   * Returns all existing roles or roles based on given ids.
   *
   * @param array|null $rids
   *   Array of role ids to load.
   *
   * @return array
   *   Array of roles.
   */
  public function getAllRoles(?array $rids = NULL);

  /**
   * Returns configurable roles.
   *
   * @return array
   *   The configurable roles.
   */
  public function getConfigurableRoles();

  /**
   * Returns the role the current user is masquerading as.
   *
   * @return array
   *   User roles.
   */
  public function getRoles($uid);

  /**
   * Sets roles in user data.
   *
   * @param mixed $uid
   *   The user ID to set roles for.
   * @param array $roles
   *   The roles to set in user data.
   *
   * @return mixed
   */
  public function setRoles($uid, array $roles);

  /**
   * Whether the current user is masquerading.
   *
   * @return bool
   *   Whether masquerade role is active.
   */
  public function isActive($uid);

  /**
   * Sets active state in user data.
   *
   * @param mixed $uid
   *   The user id to set module active for.
   * @param bool  $active
   *   TRUE for active, FALSE for inactive.
   *
   * @return mixed
   */
  public function setActive($uid, bool $active);

  /**
   * Returns user data from config.
   *
   * @param mixed  $uid
   *   The user ID to get user data for.
   * @param string $key
   *   The user data key to return.
   *
   * @return null|array|mixed
   *   The requested data.
   */
  public function getData($uid, string $key);

  /**
   * Sets user data based on provided key/value.
   *
   * @param mixed  $uid
   *   The user ID to set user data for.
   * @param string $key
   *   The user data key to set.
   * @param mixed  $value
   *   The value to set for user data key.
   *
   * @return mixed
   *   The result of setting the data.
   */
  public function setData($uid, string $key, $value);

  /**
   * Removes user data.
   *
   * @param mixed  $uid
   *   The user ID to remove user data from.
   * @param string $key
   *   The user data key to remove.
   *
   * @return mixed
   *   The result of removing the data.
   */
  public function removeData($uid, string $key);

  /**
   * Resets cache for given user id.
   *
   * @param string|int $uid
   *   The user ID to reset cache tags for.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function invalidateTags($uid);

}
