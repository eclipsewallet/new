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


namespace Mirasvit\Indexer\Plugin;

use Mirasvit\Indexer\Api\Service\HistoryServiceInterface;

/** @var \Magento\Framework\Mview\ActionInterface $di */
class MviewActionPlugin
{
    /**
     * @var HistoryServiceInterface
     */
    private $historyService;

    public function __construct(
        HistoryServiceInterface $historyService
    ) {
        $this->historyService = $historyService;
    }

    //    public function beforeExecute($mview, $ids)
    //    {
    //        $module = explode('\\', get_class($mview))[1];
    //        self::$lastHistoryId = $this->historyService->start($module, __('Reindex %1 entity(s)', count($ids)));
    //        file_put_contents(dirname(__FILE__).'/a.log', self::$lastHistoryId.PHP_EOL, FILE_APPEND);
    //    }
    //
    //    public function afterExecute($mview, $ids)
    //    {
    //        file_put_contents(dirname(__FILE__).'/a.log', self::$lastHistoryId.PHP_EOL, FILE_APPEND);
    //        $this->historyService->finish(self::$lastHistoryId);
    //    }
}