<?php
/**
 * Created by PhpStorm.
 * User: nvtro
 * Date: 11/16/2018
 * Time: 11:32 AM
 */

namespace Wiki\VAT\Block\Order\View;

use Magento\Framework\DataObject;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Wiki\VAT\Helper\Data as VatHelper;

class VatInformation extends  Template
{
    /**
     * @type Registry|null
     */
    protected $registry = null;

    /**
     * @var VatHelper
     */
    protected $vatHelper;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param vatHelper $vatHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        VatHelper $vatHelper,
        array $data = []
    )
    {
        $this->registry   = $registry;
        $this->vatHelper = $vatHelper;

        parent::__construct($context, $data);
    }

    /**
     * Get vat information
     *
     * @return DataObject
     */
    public function getVatInformation()
    {
        $result = [];

        if ($order = $this->getOrder()) {
            $vatInformation = $order->getWkVatInformation();

            if (is_array(json_decode($vatInformation, true))) {
                $result = json_decode($vatInformation, true);
            } else {
                $values = explode(' ', $vatInformation);
                if (sizeof($values) > 1) {
                    $result['vatNumber'] = $values[0];
                    $result['vatCompany'] = $values[1];
                    $result['vatAddress'] = $values[2];
                    $result['vatSave'] = $values[3];
                }
            }
        }

        return new DataObject($result);
    }

    /**
     * Get current order
     *
     * @return mixed
     */
    public function getOrder()
    {
        return $this->registry->registry('current_order');
    }
}