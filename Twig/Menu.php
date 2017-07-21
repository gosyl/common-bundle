<?php
namespace Gosyl\CommonBundle\Twig;

use Gosyl\CommonBundle\Twig\Menu\AbstractMenu;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

class Menu extends \Twig_Extension {
    /**
     * @var Router
     */
    protected $router;

    /**
     * @var array
     */
    protected $aNamespaces;

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
     * @var Container
     */
    protected $container;

    /**
     * Menu constructor.
     *
     * @param Router $router
     * @param AuthorizationChecker $autorization
     */
    public function __construct(Router $router, AuthorizationChecker $autorization, Container $container) {
        $this->router = $router;
        $this->autorizationChecker = $autorization;
        $this->container = $container;

        $this->aNamespaces = $this->container->getParameter('menu');
    }

    public function getFunctions() {
        return array(
            new \Twig_SimpleFunction('menu', array($this, 'menuFunction'), array('is_safe' => array('html')))
        );
    }

    public function menuFunction($routeActuelle) {
//        $sContenu = '';
        $contenu = [];
        foreach($this->aNamespaces as $oSubMenu => $bundle) {
            if($bundle == 'Common') {
                foreach($this->aMenu as $classMenu) {
                    if (!in_array($classMenu, $this->aClassesLoaded)) {
                        $oClass = $oSubMenu . '\\' . $classMenu;
                        /**
                         * @var AbstractMenu $oMenu
                         */
                        $oMenu = new $oClass($this->router, $this->autorizationChecker, $this->aNamespaces);

                        if ($oMenu->getUrl() != $routeActuelle) {
//                            $sContenu .= $oMenu->getLink();
                            $contenu[] = $oMenu->getLink();
                        }
                        $this->aClassesLoaded[sizeof($contenu) - 1] = $classMenu;
                    }
                }
            } else {
                $oInst = new $oSubMenu();
                foreach($oInst->aMenu as $classMenu) {
                    $oClass = $oSubMenu.'\\'.$classMenu;
                    /**
                     * @var AbstractMenu $oMenu
                     */
                    $oMenu = new $oClass($this->router, $this->autorizationChecker, $this->aNamespaces);
                    if (!in_array($classMenu, $this->aClassesLoaded)) {
                        if ($oMenu->getUrl() != $routeActuelle) {
//                            $sContenu .= $oMenu->getLink();
                            $contenu[] = $oMenu->getLink();
                        }
                        $this->aClassesLoaded[sizeof($contenu) - 1] = $classMenu;
                    } else {
                        foreach($this->aClassesLoaded as $pos => $value) {
                            if($value == $classMenu) {
                                if($oMenu->getUrl() != $routeActuelle) {
                                    $contenu[$pos] = array_merge($contenu[$pos], $oMenu->getLink());
                                }
                            }
                        }
                    }
                }
            }
        }

        $sContenu = '';

        foreach($contenu as $key => $value) {
            if(!empty($value)) {
                if(!is_null($value['lien'])) {
                    //        return '<li><a href="'.$sLien.'">' . $this->sTitle . '</a></li>';
                    $sContenu .= '<li><a href="'.$value['lien'].'">' . $value['titre'] . '</a></li>';
                } elseif(is_array($value['subMenu'])) {
                    $sContenu .= '<li class="dropdown">';
                    $sContenu .= '<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">' . $value['titre'] . '</a>';
                    // ouverture du sous menu
                    $sContenu .= '<ul class="dropdown-menu">';

                    foreach($value['subMenu'] as $subKey => $subMenu) {
                        if(!$subMenu) {
                            $sContenu .= '<li class="divider" role="separator"></li>';
                        } elseif(is_array($subMenu)) {
                            $sContenu .= '<li><a href="'.$subMenu['lien'].'">' . $subMenu['titre'] . '</a></li>';
                        }
                    }
                    $sContenu .= '</ul>';
                    $sContenu .= '</li>';
                }
            }
        }

        return $sContenu;
    }

    public function getName() {
        return 'twig.extension.menu';
    }
}