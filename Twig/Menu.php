<?php
namespace Gosyl\CommonBundle\Twig;

use Gosyl\CommonBundle\Twig\Menu\AbstractMenu;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

class Menu extends \Twig_Extension
{
    /**
     * @var Router
     */
    protected $router;

    /**
     * @var AuthorizationChecker
     */
    protected $autorizationChecker;

    /**
     * @var array
     */
    protected $aMenu = array(
        'Connexion',
        //'Inscription',
        'Administration',
        'Deconnexion',

    );

    /**
     * Menu constructor.
     * @param Router $router
     * @param AuthorizationChecker $autorization
     */
    public function __construct(Router $router, AuthorizationChecker $autorization) {
        $this->router = $router;
        $this->autorizationChecker = $autorization;
    }

    public function getFunctions() {
        return array(
            new \Twig_SimpleFunction('menu', array($this, 'menuFunction'), array('is_safe' => array('html')))
        );
    }

    public function menuFunction($routeActuelle) {
        $sContenu = '';
        foreach($this->aMenu as $menu) {
            $sClassName = "\\" . __NAMESPACE__ . "\\" . 'Menu' . "\\" . $menu;
            /**
             * @var AbstractMenu $oMenu
             */
            $oMenu = new $sClassName($this->router, $this->autorizationChecker);
            if($oMenu->getUrl() != $routeActuelle) {
                $sContenu .= $oMenu->getLink();
            }

        }

        return $sContenu;
    }

    public function getName() {
        return 'twig.extension.menu';
    }
}