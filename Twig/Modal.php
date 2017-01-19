<?php
/**
 * Created by PhpStorm.
 * User: alexandre
 * Date: 18/01/17
 * Time: 21:33
 */

namespace Gosyl\CommonBundle\Twig;


class Modal extends \Twig_Extension
{
    /**
     * @var array
     */
    protected $aOptions;

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('modal', array($this, 'modalFunction'), array('is_safe' => array('html')))
        );
    }

    /**
     * $aOptions ['footer' => [
     *                   'text' => [],
     *                   'buttons' => [
     *                       [
     *                           'id' => "",
     *                           'class' => ["", ""] | "",
     *                           'forClose' => true|false,
     *                           'label' => ""
     *                       ],
     *                       [...]
     *                   ]
     *               ]
     *           ]
     *
     * @param string $sTitle
     * @param string $sBody
     * @param array $aOptions
     * @return string
     */
    public function modalFunction($sTitle = '', $sBody = '', array $aOptions = array())
    {
        $this->aOptions = $aOptions;

        $sContenu = '<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">';
        $sContenu .= '<div class="modal-dialog" role="document">';
        $sContenu .= '<div class="modal-content">';

        $sContenu .= $this->_getHeader($sTitle);
        $sContenu .= $this->_getBody($sBody);
        $sContenu .= $this->_getFooter();

        $sContenu .= '</div>';
        $sContenu .= '</div>';
        $sContenu .= '</div>';
        return $sContenu;
    }

    public function getName()
    {
        return 'twig.extension.modal';
    }

    protected function _getBody($sBody)
    {
        return '<div class="modal-body">' . $sBody . '</div>';
    }

    protected function _getFooter()
    {
        $sContenu = '<div class="modal-footer">';

        if (count($this->aOptions) != 0 && array_key_exists('footer', $this->aOptions)) {
            $aFooterContenu = $this->aOptions['footer'];
            if (array_key_exists('text', $aFooterContenu)) {
                $sContenu .= $this->_getFooterText($aFooterContenu['text']);
            }

            if (array_key_exists('buttons', $aFooterContenu)) {
                $sContenu .= $this->_getFooterButtons($aFooterContenu['buttons']);
            }
        }

        $sContenu .= '</div>';
        return $sContenu;
    }

    protected function _getFooterButtonClass(array $aButton)
    {
        $sContenu = '';

        if (array_key_exists('class', $aButton)) {
            if (is_string($aButton['class'])) {
                $aButton['class'] = array($aButton['class']);
            }

            if (is_array($aButton['class'])) {
                foreach ($aButton['class'] as $sClass) {
                    $sContenu .= ' ' . $sClass;
                }
            }
        }

        return $sContenu;
    }

    protected function _getFooterData(array $aButton)
    {
        $sContenu = '';

        if (array_key_exists('forClose', $aButton)) {
            if ($aButton['forClose']) {
                $sContenu .= ' data-dismiss="modal"';
            }
        }

        return $sContenu;
    }

    protected function _getFooterButtons(array $aButtons)
    {
        $sContenu = '';

        foreach ($aButtons as $aButton) {
            $sContenu .= '<button type="button" id="' . $aButton['id'] . '" class="btn' . $this->_getFooterButtonClass($aButton) . '"' . $this->_getFooterData($aButton) . '>' . $aButton['label'] . '</button>';
        }

        return $sContenu;
    }

    protected function _getFooterText(array $aTexts)
    {
        $sContenu = '';

        foreach ($aTexts as $text) {
            $sContenu .= '<p>' . $text . '</p>';
        }

        return $sContenu;
    }

    protected function _getHeader($sTitle)
    {
        $sContenu = '<div class="modal-header">';
        $sContenu .= '<button type="button" class="close" data-dismiss="modal" aria-label="Fermer">
            <span aria-hidden="true">&times;</span>
        </button>';
        $sContenu .= '<h4 class="modal-title" id="myModalLabel">' . $sTitle . '</h4>';

        $sContenu .= '</div>';
        return $sContenu;
    }
}