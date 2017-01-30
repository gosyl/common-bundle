<?php
namespace Gosyl\CommonBundle\Twig;

use Gosyl\CommonBundle\Twig\Menu\AbstractMenu;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

class Menu extends \Twig_Extension {
    /**
     * @var Router
     */
    protected $router;

    /**
     * @var array
     */
    protected $aNamespaces = array(
        "\\" . __NAMESPACE__ . "\\" . 'Menu' . "\\" => 'Common'
    );

    /**
     * @var AuthorizationChecker
     */
    protected $autorizationChecker;

    /**
     * @var array
     */
    protected $aMenu = array(
        0 => 'Connexion',
        //97 => 'Inscription',
        98 => 'Administration',
        99 => 'Deconnexion',
    );

    /**
     * @var array
     */
    protected $aClassesLoaded = array();

    /**
     * @param array $childMenu
     * @param array $childNamespace
     */
    protected function mergeNamespace(array $childMenu = array(), array $childNamespace = array()) {
        $this->aNamespaces = array_merge($this->aNamespaces, $childNamespace);
        $this->aMenu = $this->aMenu + $childMenu;
        ksort($this->aMenu);
    }

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
            foreach ($this->aNamespaces as $namespace => $bundle) {
                $sClassName = $namespace . $menu;
                if (class_exists($sClassName)) {
                    // On teste si la classe existe dans la bundle enfant
                    foreach ($this->aNamespaces as $newNamespace => $newBundle) {
                        if ($newNamespace == $namespace) {
                            continue;
                        } elseif (class_exists($newNamespace . $menu) && $newBundle != 'Common') {
                            $sClassName = $newNamespace . $menu;
                            break;
                        }
                    }

                    if (!in_array($sClassName, $this->aClassesLoaded)) {
                        /**
                         * @var AbstractMenu $oMenu
                         */
                        $oMenu = new $sClassName($this->router, $this->autorizationChecker, $this->aNamespaces);
                        $this->aClassesLoaded[] = $sClassName;
                        if ($oMenu->getUrl() != $routeActuelle) {
                            $sContenu .= $oMenu->getLink();
                        }
                    }

                }
            }
        }//die;

        return $sContenu;
    }

    public function getName() {
        return 'twig.extension.menu';
    }
}