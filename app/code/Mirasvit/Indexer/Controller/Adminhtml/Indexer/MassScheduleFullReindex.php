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



namespace Mirasvit\Indexer\Controller\Adminhtml\Indexer;

use Mirasvit\Indexer\Api\Data\StateInterface;
use Mirasvit\Indexer\Controller\Adminhtml\Indexer;

class MassScheduleFullReindex extends Indexer
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $indexerIds = $this->getRequest()->getParam('indexer_ids', false);

        if ($indexerIds && !is_array($indexerIds)) {
            $indexerIds = [$indexerIds];
        }

        if (!is_array($indexerIds)) {
            $this->messageManager->addErrorMessage(__('Please select indexers.'));
        } else {
            try {
                $states = $this->stateCollectionFactory->create();

                foreach ($indexerIds as $indexerId) {
                    foreach ($states->getItems() as $state) {
                        /** @var \Magento\Indexer\Model\Indexer\State $state */
                        if ($state->getIndexerId() == $indexerId) {
                            $state->setStatus(StateInterface::STATUS_SCHEDULED);
                            $state->save();
                        }
                    }
                }

                $this->messageManager->addSuccessMessage(
                    __('Full reindex of %1 indexer(s) are scheduled.', count($indexerIds))
                );
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __("We couldn't change indexer(s)' mode because of an error.")
                );
            }
        }

        $this->_redirect('indexer/indexer/list');
    }
}
