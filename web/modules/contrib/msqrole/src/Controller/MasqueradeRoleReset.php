<?php

namespace Drupal\msqrole\Controller;

use Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException;
use Drupal\Component\Plugin\Exception\PluginNotFoundException;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Routing\RedirectDestinationInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Url;
use Drupal\msqrole\RoleManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class MasqueradeRoleReset
 *
 * @package Drupal\msqrole\Controller
 */
class MasqueradeRoleReset extends ControllerBase {

  /**
   * The role manager.
   *
   * @var \Drupal\msqrole\RoleManagerInterface
   */
  protected $roleManager;

  /**
   * The redirect destination object.
   *
   * @var \Drupal\Core\Routing\RedirectDestinationInterface
   */
  protected $destination;

  /**
   * MasqueradeRoleReset constructor.
   *
   * @param \Drupal\msqrole\RoleManagerInterface $role_manager
   *   The role manager.
   * @param \Drupal\Core\Routing\RedirectDestinationInterface $destination
   *   The redirect destination object.
   */
  public function __construct(RoleManagerInterface $role_manager, RedirectDestinationInterface $destination) {
    $this->roleManager = $role_manager;
    $this->destination = $destination;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('msqrole.manager'),
      $container->get('redirect.destination')
    );
  }

  /**
   * Resets msqrole data and redirects back to front.
   *
   *
   * @return \Symfony\Component\HttpFoundation\RedirectResponse
   *   The redirect response object.
   */
  public function reset() {
    $this->roleManager->removeData($this->currentUser()->id());

    // Invalidate cache tags
    try {
      $this->roleManager->invalidateTags($this->currentUser()->id());
    } catch (InvalidPluginDefinitionException | PluginNotFoundException $e) {
    }

    try {
      $destination = Url::fromUserInput($this->destination->get());
      if ($destination->isRouted() && $destination->getRouteName() !== 'msqrole.reset') {
        return $this->redirect($destination->getRouteName(), $destination->getRouteParameters());
      }
    } catch(\Exception $e) {
    }
    return $this->redirect('<front>');
  }

  /**
   * Checks whether msqrole is active or not.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Run access checks for this account.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access result.
   */
  public function access(AccountInterface $account) {
    return AccessResult::allowedIf($this->roleManager->isActive($account->id()));
  }

}
