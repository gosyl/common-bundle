<?php

namespace Gosyl\CommonBundle\Event;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\HttpKernel;
use Gosyl\CommonBundle\Entity\ParamUsers;

class ActivityListener {

	/**
	 *
	 * @var TokenStorage
	 */
	protected $_oTokenStorage;

	/**
	 *
	 * @var EntityManager
	 */
	protected $_oEntityManager;

	/**
	 *
	 * @var Container
	 */
	protected $_oContainer;

	public function __construct(EntityManager $oDoctrine, Container $oContainer, TokenStorage $oToken) {
		$this->_oEntityManager = $oDoctrine;
		$this->_oContainer = $oContainer;
		$this->_oTokenStorage = $oToken;
	}

	/**
	 * Update the user "lastActivity" on each request
	 * 
	 * @param FilterControllerEvent $event
	 */
	public function onCoreController(FilterControllerEvent $event) {
		// ici nous vérifions que la requête est une "MASTER_REQUEST" pour que les sous-requête soit ingoré (par exemple si vous faites un render() dans votre template)
		if($event->getRequestType() !== HttpKernel::MASTER_REQUEST) {
			return;
		}
		
		// Nous vérifions qu'un token d'autentification est bien présent avant d'essayer manipuler l'utilisateur courant.
		if($this->_oTokenStorage->getToken()) {
			/**
			 * @var ParamUsers $user
			 */
			$user = $this->_oTokenStorage->getToken()->getUser();
			
			// Nous vérifions que l'utilisateur est bien du bon type pour ne pas appeler getLastActivity() sur un objet autre objet User
			if($user instanceof ParamUsers && $user->isActiveNow()) {
				$user->setLastActivityAt(new \DateTime('now'));
				$this->_oEntityManager->flush($user);
			}
		}
	}
}