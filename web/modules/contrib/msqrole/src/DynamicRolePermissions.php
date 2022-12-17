<?php

namespace Drupal\msqrole;

use Drupal\user\RoleInterface;

/**
 * Class DynamicRolePermissions.
 *
 * @package Drupal\msqrole
 */
class DynamicRolePermissions {

  /**
   * Returns dynamically created permissions for roles.
   *
   * @return array
   *   The permissions to create a permission for.
   */
  public static function callback() {
    /**
     * @var \Drupal\msqrole\RoleManagerInterface $role_manager
     */
    $role_manager = \Drupal::service('msqrole.manager');
    $roles = $role_manager->getConfigurableRoles();
    $permissions = [];

    /**
     * @var \Drupal\user\RoleInterface $role
     */
    foreach ($roles as $role) {
      $permissions['masquerade as ' . $role->id()] = [
        'title' => t('Masquerade as :role', [
          ':role' => $role->label(),
        ]),
        'description' => t('Users with this permission will be able to masquerade as the role: :role', [
          ':role' => $role->label(),
        ]),
      ];
    }

    return $permissions;
  }

}
