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
 * @package   mirasvit/module-cache-warmer
 * @version   1.2.12
 * @copyright Copyright (C) 2019 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\CacheWarmer\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Mirasvit\CacheWarmer\Api\Data\WarmRuleInterface;
use Mirasvit\CacheWarmer\Api\Repository\WarmRuleRepositoryInterface;
use Mirasvit\CacheWarmer\Model\ResourceModel\Job\CollectionFactory;

abstract class AbstractWarmRule extends Action
{
    /**
     * @var WarmRuleRepositoryInterface
     */
    protected $WarmRuleRepository;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var Context
     */
    protected $context;

    /**
     * @var \Magento\Framework\Controller\ResultFactory
     */
    protected $resultFactory;

    public function __construct(
        WarmRuleRepositoryInterface $WarmRuleRepository,
        Registry $registry,
        CollectionFactory $collectionFactory,
        Context $context
    ) {
        $this->WarmRuleRepository = $WarmRuleRepository;
        $this->registry          = $registry;

        $this->context       = $context;
        $this->resultFactory = $context->getResultFactory();

        parent::__construct($context);
    }

    /**
     * @return WarmRuleInterface
     */
    protected function initModel()
    {
        $model = $this->WarmRuleRepository->create();

        if ($this->getRequest()->getParam(WarmRuleInterface::ID)) {
            $model = $this->WarmRuleRepository->get($this->getRequest()->getParam(WarmRuleInterface::ID));
        }

        $this->registry->register(WarmRuleInterface::class, $model);

        return $model;
    }

    /**
     * @param \Magento\Backend\Model\View\Result\Page $resultPage
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function initPage($resultPage)
    {
        $resultPage->setActiveMenu('Mirasvit_CacheWarmer::cache_warmer_job_rule');
        $resultPage->getConfig()->getTitle()->prepend(__('Page Cache Warmer'));
        $resultPage->getConfig()->getTitle()->prepend(__('Warm Rules'));

        return $resultPage;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->context->getAuthorization()->isAllowed('Mirasvit_CacheWarmer::cache_warmer_warm_rule');
    }
}
