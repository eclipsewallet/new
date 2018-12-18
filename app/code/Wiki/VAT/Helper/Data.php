<?php
/**
 * Created by PhpStorm.
 * User: nvtro
 * Date: 11/15/2018
 * Time: 2:52 PM
 */

namespace Wiki\VAT\Helper;
use Wiki\VAT\Helper\AbstractData;

class Data extends AbstractData
{
    const CONFIG_MODULE_PATH = 'wiki';

    /**
     * @param null $store
     * @return bool
     */
    public function isDisabled($store = null)
    {
        return !$this->isEnabled($store);
    }

}