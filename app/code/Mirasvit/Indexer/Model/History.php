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



namespace Mirasvit\Indexer\Model;

use Magento\Framework\Model\AbstractModel;
use Mirasvit\Indexer\Api\Data\HistoryInterface;

class History extends AbstractModel implements HistoryInterface
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('Mirasvit\Indexer\Model\ResourceModel\History');
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * {@inheritdoc}
     */
    public function getStartedAt()
    {
        return $this->getData(self::STARTED_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function setStartedAt($input)
    {
        return $this->setData(self::STARTED_AT, $input);
    }

    /**
     * {@inheritdoc}
     */
    public function getExecutionTime()
    {
        return $this->getData(self::EXECUTION_TIME);
    }

    /**
     * {@inheritdoc}
     */
    public function setExecutionTime($input)
    {
        return $this->setData(self::EXECUTION_TIME, $input);
    }

    /**
     * {@inheritdoc}
     */
    public function getIndexerId()
    {
        return $this->getData(self::INDEXER_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setIndexerId($input)
    {
        return $this->setData(self::INDEXER_ID, $input);
    }

    /**
     * {@inheritdoc}
     */
    public function getSummary()
    {
        return $this->getData(self::SUMMARY);
    }

    /**
     * {@inheritdoc}
     */
    public function setSummary($input)
    {
        return $this->setData(self::SUMMARY, $input);
    }

    /**
     * {@inheritdoc}
     */
    public function getMessage()
    {
        return $this->getData(self::MESSAGE);
    }

    /**
     * {@inheritdoc}
     */
    public function setMessage($input)
    {
        return $this->setData(self::MESSAGE, $input);
    }

    /**
     * {@inheritdoc}
     */
    public function getTickAt()
    {
        return $this->getData(self::TICK_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function setTickAt($input)
    {
        return $this->setData(self::TICK_AT, $input);
    }
}
