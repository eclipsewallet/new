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


namespace Mirasvit\Indexer\Api\Service;

use Mirasvit\Indexer\Api\Data\HistoryInterface;

interface HistoryServiceInterface
{
    /**
     * @param string $indexerId
     * @param string $summary
     * @return int
     */
    public function start($indexerId, $summary);

    /**
     * @param int    $historyId
     * @param string $message
     * @return bool
     */
    public function finish($historyId, $message = '');

    /**
     * @param int $historyId
     * @return bool
     */
    public function tick($historyId);

    /**
     * @param int $historyId
     * @return bool
     */
    public function isLocked($historyId);

    /**
     * @return HistoryInterface[]
     */
    public function getRecentHistory();
}