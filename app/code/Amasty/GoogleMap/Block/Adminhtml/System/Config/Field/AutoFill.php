<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_GoogleMap
 */


namespace Amasty\GoogleMap\Block\Adminhtml\System\Config\Field;

class AutoFill extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @var string
     */
    protected $_template = 'Amasty_GoogleMap::system/config/form/field/autofill_button.phtml';

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     *
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $this->setElement($element);

        return $this->_toHtml();
    }
}
