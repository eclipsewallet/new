<?php


namespace Marvelic\Job\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface ExportRepositoryInterface
{

    /**
     * Save Export
     * @param \Marvelic\Job\Api\Data\ExportInterface $export
     * @return \Marvelic\Job\Api\Data\ExportInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Marvelic\Job\Api\Data\ExportInterface $export
    );

    /**
     * Retrieve Export
     * @param string $exportId
     * @return \Marvelic\Job\Api\Data\ExportInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($exportId);

    /**
     * Retrieve Export matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Marvelic\Job\Api\Data\ExportSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Export
     * @param \Marvelic\Job\Api\Data\ExportInterface $export
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Marvelic\Job\Api\Data\ExportInterface $export
    );

    /**
     * Delete Export by ID
     * @param string $exportId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($exportId);
}
