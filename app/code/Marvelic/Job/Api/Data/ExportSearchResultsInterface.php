<?php


namespace Marvelic\Job\Api\Data;

interface ExportSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get Export list.
     * @return \Marvelic\Job\Api\Data\ExportInterface[]
     */
    public function getItems();

    /**
     * Set title list.
     * @param \Marvelic\Job\Api\Data\ExportInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
