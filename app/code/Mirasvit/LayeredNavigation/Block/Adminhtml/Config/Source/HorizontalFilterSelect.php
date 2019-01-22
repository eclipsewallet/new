<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-navigation
 * @version   1.0.54
 * @copyright Copyright (C) 2019 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\LayeredNavigation\Block\Adminhtml\Config\Source;

use Magento\Framework\Data\Form\Element\AbstractElement;

class HorizontalFilterSelect extends \Magento\Config\Block\System\Config\Form\Field
{
    protected function _getElementHtml(AbstractElement $element)
    {
        return parent::_getElementHtml($element) . '
        <script>
            require([
                "jquery",
                "mNavigationChosen",
                "domReady!"
            ], function ($, mNavigationChosen) {
                    $(\'#' . $element->getId() . '\').chosen({
                    width: "100%",
                    placeholder_text: \'' .  __('Select Filtered Attributes') . '\'
                });
            })
        </script>';
    }
}