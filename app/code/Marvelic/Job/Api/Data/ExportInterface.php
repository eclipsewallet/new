<?php


namespace Marvelic\Job\Api\Data;

interface ExportInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{

    const IS_ACTIVE = 'is_active';
    const SOURCE_DATA = 'source_data';
    const FILE_UPDATED_AT = 'file_updated_at';
    const TITLE = 'title';
    const ENTITY = 'entity';
    const CRON = 'cron';
    const EXPORT_SOURCE = 'export_source';
    const FREQUENCY = 'frequency';
    const BEHAVIOR_DATA = 'behavior_data';
    const EXPORT_ID = 'export_id';

    /**
     * Get export_id
     * @return string|null
     */
    public function getExportId();

    /**
     * Set export_id
     * @param string $exportId
     * @return \Marvelic\Job\Api\Data\ExportInterface
     */
    public function setExportId($exportId);

    /**
     * Get title
     * @return string|null
     */
    public function getTitle();

    /**
     * Set title
     * @param string $title
     * @return \Marvelic\Job\Api\Data\ExportInterface
     */
    public function setTitle($title);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Marvelic\Job\Api\Data\ExportExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Marvelic\Job\Api\Data\ExportExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Marvelic\Job\Api\Data\ExportExtensionInterface $extensionAttributes
    );

    /**
     * Get is_active
     * @return string|null
     */
    public function getIsActive();

    /**
     * Set is_active
     * @param string $isActive
     * @return \Marvelic\Job\Api\Data\ExportInterface
     */
    public function setIsActive($isActive);

    /**
     * Get cron
     * @return string|null
     */
    public function getCron();

    /**
     * Set cron
     * @param string $cron
     * @return \Marvelic\Job\Api\Data\ExportInterface
     */
    public function setCron($cron);

    /**
     * Get frequency
     * @return string|null
     */
    public function getFrequency();

    /**
     * Set frequency
     * @param string $frequency
     * @return \Marvelic\Job\Api\Data\ExportInterface
     */
    public function setFrequency($frequency);

    /**
     * Get entity
     * @return string|null
     */
    public function getEntity();

    /**
     * Set entity
     * @param string $entity
     * @return \Marvelic\Job\Api\Data\ExportInterface
     */
    public function setEntity($entity);

    /**
     * Get behavior_data
     * @return string|null
     */
    public function getBehaviorData();

    /**
     * Set behavior_data
     * @param string $behaviorData
     * @return \Marvelic\Job\Api\Data\ExportInterface
     */
    public function setBehaviorData($behaviorData);

    /**
     * Get export_source
     * @return string|null
     */
    public function getExportSource();

    /**
     * Set export_source
     * @param string $exportSource
     * @return \Marvelic\Job\Api\Data\ExportInterface
     */
    public function setExportSource($exportSource);

    /**
     * Get source_data
     * @return string|null
     */
    public function getSourceData();

    /**
     * Set source_data
     * @param string $sourceData
     * @return \Marvelic\Job\Api\Data\ExportInterface
     */
    public function setSourceData($sourceData);

    /**
     * Get file_updated_at
     * @return string|null
     */
    public function getFileUpdatedAt();

    /**
     * Set file_updated_at
     * @param string $fileUpdatedAt
     * @return \Marvelic\Job\Api\Data\ExportInterface
     */
    public function setFileUpdatedAt($fileUpdatedAt);
}
