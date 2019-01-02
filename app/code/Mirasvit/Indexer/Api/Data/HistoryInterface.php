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


namespace Mirasvit\Indexer\Api\Data;

interface HistoryInterface
{
    const ID             = 'history_id';
    const STARTED_AT     = 'started_at';
    const EXECUTION_TIME = 'execution_time';
    const INDEXER_ID     = 'indexer_id';
    const SUMMARY        = 'summary';
    const MESSAGE        = 'message';
    const TICK_AT        = 'tick_at';

    /**
     * @return int
     */
    public function getId();

    /**
     * @return string
     */
    public function getStartedAt();

    /**
     * @param string $input
     * @return $this
     */
    public function setStartedAt($input);

    /**
     * @return int
     */
    public function getExecutionTime();

    /**
     * @param int $input
     * @return $this
     */
    public function setExecutionTime($input);

    /**
     * @return string
     */
    public function getIndexerId();

    /**
     * @param string $input
     * @return $this
     */
    public function setIndexerId($input);

    /**
     * @return string
     */
    public function getSummary();

    /**
     * @param string $input
     * @return $this
     */
    public function setSummary($input);

    /**
     * @return string
     */
    public function getMessage();

    /**
     * @param string $input
     * @return $this
     */
    public function setMessage($input);

    /**
     * @return string
     */
    public function getTickAt();

    /**
     * @param string $input
     * @return $this
     */
    public function setTickAt($input);
}