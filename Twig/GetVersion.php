<?php
/**
 * Created by PhpStorm.
 * User: alexandre
 * Date: 29/08/17
 * Time: 16:10
 */

namespace Gosyl\CommonBundle\Twig;


class GetVersion extends \Twig_Extension {
    public function getFunctions() {
        return array(
            new \Twig_SimpleFunction('getVersion', array($this, 'getVersionFunction'), array('is_safe' => array('html')))
        );
    }

    public function getVersionFunction() {
        if(defined('ENVIRONNEMENT') && ENVIRONNEMENT == 'developpement') {//Récup du num de commit
            $commitHash = 'Commit : ' . trim(exec('git rev-parse --abbrev-ref HEAD')) . '-' . trim(exec('git log --pretty="%h" -n1 HEAD'));
        } else {
            $commitHash = trim(exec('git describe origin/master  --tags --abbrev=0'));
        }

        return $commitHash;
    }

    public function getName() {
        return 'twig.extension.getVersion';
    }
}