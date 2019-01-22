<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_AutoCancelOrder
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\AutoCancelOrder\Block\Adminhtml\System\Config\Button;
 
class ShowLog extends \Magento\Config\Block\System\Config\Form\Field
{
    const BUTTON_TEMPLATE = 'button/buttons.phtml';

    /**
     * Set button template
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (!$this->getTemplate()) {
            $this->setTemplate(static::BUTTON_TEMPLATE);
        }
        return $this;
    }

    /**
     * Get button html
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return string
     */
    public function getButtonHtml()
    {
        $url = $this->getUrl('autocancelorder/cancellog/index');
        $button = $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Button'
        )->setData(
            [
                'id'        => 'show_log_button',
                'label'     => __('Show Log'),
                'onclick'   => 'window.open( \'' .$url.
                    '\')',
                'target' => '_blank'
            ]
        );
 
        return $button->toHtml();
    }

    /**
     * Get element html
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        return $this->_toHtml();
    }
}
