<?php


namespace Marvelic\Job\Controller\Adminhtml\Export;

use Magento\Framework\Exception\LocalizedException;

class Save extends \Magento\Backend\App\Action
{

    protected $dataPersistor;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
    ) {
        $this->dataPersistor = $dataPersistor;
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();

        /* Date Config */
        $dataDateConfig = [
            'type_time'    => $data['type_time'],
            'period'    => $data['period']
        ];

        /* Export Source */
        $dataExportSource = [
            'type'      => $data['export_source_job'],
            'file_path' => $data['file_path'],
            'host'      => $data['host'],
            'port'      => $data['port'],
            'username'  => $data['username'],
            'password'  => $data['password']
        ];

        $data['date_config']    = json_encode($dataDateConfig);
        $data['export_source']  = json_encode($dataExportSource);
        $data['file_updated_at'] = date("Y:m:d h:i:s");

        if ($data) {
            $id = $this->getRequest()->getParam('export_id');
        
            $model = $this->_objectManager->create(\Marvelic\Job\Model\Export::class)->load($id);
            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This Export no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }
        
            $model->setData($data);
        
            try {
                $model->save();
                $this->messageManager->addSuccessMessage(__('You saved the Export.'));
                $this->dataPersistor->clear('marvelic_job_export');
        
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['export_id' => $model->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Export.'));
            }
        
            $this->dataPersistor->set('marvelic_job_export', $data);
            return $resultRedirect->setPath('*/*/edit', ['export_id' => $this->getRequest()->getParam('export_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
