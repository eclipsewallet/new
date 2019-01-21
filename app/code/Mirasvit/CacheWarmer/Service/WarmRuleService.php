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



namespace Mirasvit\CacheWarmer\Service;

use Mirasvit\CacheWarmer\Api\Data\PageInterface;
use Mirasvit\CacheWarmer\Api\Data\WarmRuleInterface;
use Mirasvit\CacheWarmer\Api\Repository\PageRepositoryInterface;
use Mirasvit\CacheWarmer\Api\Repository\WarmRuleRepositoryInterface;

class WarmRuleService
{
    private $ruleRepository;

    private $serializer;

    public function __construct(
        PageRepositoryInterface $pageRepository,
        WarmRuleRepositoryInterface $ruleRepository,
        \Magento\Framework\App\Http\Context $context,
        \Mirasvit\Core\Service\CompatibilityService $compatibilityService
    ) {
        $this->ruleRepository       = $ruleRepository;
        $this->pageRepository       = $pageRepository;
        $this->compatibilityService = $compatibilityService;

        if (!$this->compatibilityService->is20() && !$this->compatibilityService->is21()) {
            $this->serializer = $this->compatibilityService->getObjectManager()
                ->create("\Magento\Framework\Serialize\Serializer\Json");
        }
    }

    /**
     * @see Magento\Framework\App\PageCache\Identifier
     * @param PageInterface $page
     * @return string
     */
    public function getCacheId(PageInterface $page)
    {
        $isSecure = strpos($page->getUri(), "https://") !== false;
        $data     = [
            $isSecure,
            $page->getUri(),
            $page->getVaryString(),
        ];
        if ($this->compatibilityService->is20() || $this->compatibilityService->is21()) {
            return md5(serialize($data));
        } else {
            return sha1($this->serializer->serialize($data));
        }
    }

    /**
     * @param PageInterface     $page
     * @param WarmRuleInterface $rule
     * @return PageInterface
     */
    public function modifyPage(PageInterface $page, WarmRuleInterface $rule = null)
    {
        if (!$rule) {
            return $page;
        }

        if (!$rule->getHeaders() && !$rule->getVaryData()) {
            return $page;
        }
        $p = clone $page;
        if ($rule->getVaryData()) {
            $varyData = array_merge($p->getVaryData(), $rule->getVaryData());
            if (isset($varyData["customer_group"])) {
                $varyData["customer_group"] = (string)$varyData["customer_group"];
                $varyData["customer_logged_in"] = true;
            }
            $p->setVaryData($varyData);
        }

        if ($rule->getHeaders()) {
            $p->setHeaders($rule->getHeaders());
        }
        $p->setCacheId($this->getCacheId($p));

        return $p;
    }

    /**
     * @return void
     */
    public function refreshPagesByRules()
    {
        $jobRuleCollection = $this->ruleRepository->getCollection();
        $versions          = [];
        foreach ($jobRuleCollection as $rule) {
            $versions[] = $rule->getConditionsSerialized();
        }
        $version = md5(implode("|", $versions));

        $pageCollection = $this->pageRepository->getCollection();
        $pageCollection->getSelect()
            ->where(PageInterface::WARM_RULE_VERSION .
                " != ? OR ISNULL(" . PageInterface::WARM_RULE_VERSION . ")", $version);

        /** @var PageInterface $page */
        while ($page = $pageCollection->fetchItem()) {
            $ruleIds = [];

            foreach ($jobRuleCollection as $jobRule) {
                if ($jobRule->getRule()->validate($page)) {
                    $ruleIds[] = $jobRule->getId();
                }
            }

            $page->setWarmRuleIds($ruleIds);
            $page->setWarmRuleVersion($version);
            $this->pageRepository->save($page);
        }
    }
}