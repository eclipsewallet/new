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
 * @package   mirasvit/module-indexer
 * @version   1.0.12
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\Indexer\Block\Adminhtml;

use Magento\Backend\Block\Template;
use Mirasvit\Indexer\Model\Config;


class History extends Template
{
    /**
     * @var Config
     */
    private $config;

    public function __construct(
        Config $config,
        Template\Context $context
    ) {
        $this->config = $config;
        parent::__construct($context);
    }

    /**
     * @return string
     */
    public function getLastRunTime()
    {
        return $this->config->getLastRunTime();
    }
}