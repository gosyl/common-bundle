<?php
/**
 * Created by PhpStorm.
 * User: alexandre
 * Date: 26/09/17
 * Time: 13:10
 */

namespace Gosyl\CommonBundle\Event;


use Gosyl\CommonBundle\Entity\ParamUsers;
use Monolog\Logger;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\PostResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class MyLogEvent {
    /**
     * @var Logger
     */
    protected $_oLogger;

    /**
     * @var array
     */
    protected $_aLog = [];

    /**
     * @var ContainerInterface
     */
    protected $_oContainer;

    /**
     * @var ParamUsers
     */
    protected $_oUser;

    public function __construct(Logger $oLogger, ContainerInterface $oContainer) {
        $this->_oLogger = $oLogger;
        $this->_oContainer = $oContainer; //dump($oSession);die;
        //$this->_oUser = $this->_oTokenStorage->getToken() ? $this->_oTokenStorage->getToken()->getUser(): 'not connected';
    }

    public function initLog(GetResponseEvent $event) {
        $oDate = new \DateTime();
        $this->_oUser = $this->_oContainer->get('security.token_storage')->getToken() ? $this->_oContainer->get('security.token_storage')->getToken()->getUser() : 'not connected';
        // log des appels php seulement
        $routeParams = $event->getRequest()->attributes->get('_route_params');
        $bEstAppelNonDesire = isset($routeParams['_format']);
        // on ne prend pas les appels pour la barre de debug
        $bEstAppelDebug = in_array($event->getRequest()->attributes->get('_route'), array('_wdt', '_profiler'));

        if(!$bEstAppelDebug && !$bEstAppelNonDesire) {
            $parameters = $event->getRequest()->request->all();
            if(!count($parameters)) {
                $parameters = new \stdClass();
            }

            $this->_aLog = [
                'server_address' => $event->getRequest()->server->get('SERVER_ADDR'),
                'timestamp' => $oDate->format('d/m/Y H:i:s'),
                'method' => $event->getRequest()->getMethod(),
                'request_uri' => $event->getRequest()->getRequestUri(),
                'server_protocol' => $event->getRequest()->server->get('SERVER_PROTOCOL'),
                'user' => is_string($this->_oUser) ? $this->_oUser : [
                    'id' => $this->_oUser->getId(),
                    'username' => $this->_oUser->getUsername(),
                    'role' => $this->_oUser->getRoles()[0]
                ],
                'parameters' => $parameters
            ];
        }
    }

    public function setResponseStatus(FilterResponseEvent $event) {
        if(count($this->_aLog) !== 0) {
            $this->_aLog['status_code'] = $event->getResponse()->getStatusCode();
        }
    }

    public function setLog(PostResponseEvent $event) {
        if(count($this->_aLog) !== 0) {
            $this->_oLogger->info("TEST", $this->_aLog);
        }
    }
}