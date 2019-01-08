<?php


namespace Marvelic\Job\Model\Data;

use Marvelic\Job\Api\Data\ExportInterface;

class Export extends \Magento\Framework\Api\AbstractExtensibleObject implements ExportInterface
{

    /**
     * Get export_id
     * @return string|null
     */
    public function getExportId()
    {
        return $this->_get(self::EXPORT_ID);
    }

    /**
     * Set export_id
     * @param string $exportId
     * @return \Marvelic\Job\Api\Data\ExportInterface
     */
    public function setExportId($exportId)
    {
        return $this->setData(self::EXPORT_ID, $exportId);
    }

    /**
     * Get title
     * @return string|null
     */
    public function getTitle()
    {
        return $this->_get(self::TITLE);
    }

    /**
     * Set title
     * @param string $title
     * @return \Marvelic\Job\Api\Data\ExportInterface
     */
    public function setTitle($title)
    {
        return $this->setData(self::TITLE, $title);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Marvelic\Job\Api\Data\ExportExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param \Marvelic\Job\Api\Data\ExportExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Marvelic\Job\Api\Data\ExportExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

    /**
     * Get is_active
     * @return string|null
     */
    public function getIsActive()
    {
        return $this->_get(self::IS_ACTIVE);
    }

    /**
     * Set is_active
     * @param string $isActive
     * @return \Marvelic\Job\Api\Data\ExportInterface
     */
    public function setIsActive($isActive)
    {
        return $this->setData(self::IS_ACTIVE, $isActive);
    }

    /**
     * Get cron
     * @return string|null
     */
    public function getCron()
    {
        return $this->_get(self::CRON);
    }

    /**
     * Set cron
     * @param string $cron
     * @return \Marvelic\Job\Api\Data\ExportInterface
     */
    public function setCron($cron)
    {
        return $this->setData(self::CRON, $cron);
    }

    /**
     * Get frequency
     * @return string|null
     */
    public function getFrequency()
    {
        return $this->_get(self::FREQUENCY);
    }

    /**
     * Set frequency
     * @param string $frequency
     * @return \Marvelic\Job\Api\Data\ExportInterface
     */
    public function setFrequency($frequency)
    {
        return $this->setData(self::FREQUENCY, $frequency);
    }

    /**
     * Get entity
     * @return string|null
     */
    public function getEntity()
    {
        return $this->_get(self::ENTITY);
    }

    /**
     * Set entity
     * @param string $entity
     * @return \Marvelic\Job\Api\Data\ExportInterface
     */
    public function setEntity($entity)
    {
        return $this->setData(self::ENTITY, $entity);
    }

    /**
     * Get behavior_data
     * @return string|null
     */
    public function getBehaviorData()
    {
        return $this->_get(self::BEHAVIOR_DATA);
    }

    /**
     * Set behavior_data
     * @param string $behaviorData
     * @return \Marvelic\Job\Api\Data\ExportInterface
     */
    public function setBehaviorData($behaviorData)
    {
        return $this->setData(self::BEHAVIOR_DATA, $behaviorData);
    }

    /**
     * Get export_source
     * @return string|null
     */
    public function getExportSource()
    {
        return $this->_get(self::EXPORT_SOURCE);
    }

    /**
     * Set export_source
     * @param string $exportSource
     * @return \Marvelic\Job\Api\Data\ExportInterface
     */
    public function setExportSource($exportSource)
    {
        return $this->setData(self::EXPORT_SOURCE, $exportSource);
    }

    /**
     * Get source_data
     * @return string|null
     */
    public function getSourceData()
    {
        return $this->_get(self::SOURCE_DATA);
    }

    /**
     * Set source_data
     * @param string $sourceData
     * @return \Marvelic\Job\Api\Data\ExportInterface
     */
    public function setSourceData($sourceData)
    {
        return $this->setData(self::SOURCE_DATA, $sourceData);
    }

    /**
     * Get file_updated_at
     * @return string|null
     */
    public function getFileUpdatedAt()
    {
        return $this->_get(self::FILE_UPDATED_AT);
    }

    /**
     * Set file_updated_at
     * @param string $fileUpdatedAt
     * @return \Marvelic\Job\Api\Data\ExportInterface
     */
    public function setFileUpdatedAt($fileUpdatedAt)
    {
        return $this->setData(self::FILE_UPDATED_AT, $fileUpdatedAt);
    }
}
