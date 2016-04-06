<?php
namespace Gosyl\CommonBundle\Security;

use Symfony\Component\Security\Core\User\UserChecker as BaseUserChecker;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

class UserChecker extends BaseUserChecker {
	/**
	 * {@inheritdoc}
	 */
	public function checkPreAuth(UserInterface $user) {
		
		if (!$user->isAccountDeleted()) {
			$ex = new UsernameNotFoundException('User account is deleted.');
			$ex->setUsername($user->getUsername());
			throw $ex;
		}
		
		parent::checkPreAuth($user);
	}
}