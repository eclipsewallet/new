<?php

namespace Marvelic\Job\Cron;

class Export
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $_orderCollectionFactory;

    /**
     * @var \Magento\Sales\Model\Order
     */
    protected $_order;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $_timezoneInterface;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $_storeTime;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory
     */
    protected $_invoiceOrder;

    /**
     * @var \Magento\Framework\Filesystem\DirectoryList
     */
    protected $_dir;

    /**
     * @var \Magento\Framework\Filesystem\Io\File
     */
    protected $_file;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $_customer;

    /**
     * Constructor
     *
     * @param \Psr\Log\LoggerInterface $logger
     */

    /**
     * @var \Marvelic\Job\Model\ExportFactory
     */
    protected $_exportFactory;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Sales\Model\Order $order,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $storeTime,
        \Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory $invoiceOrder,
        \Magento\Framework\Filesystem\DirectoryList $dir,
        \Magento\Framework\Filesystem\Io\File $file,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        \Marvelic\Job\Model\ExportFactory $exportFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->logger                   = $logger;
        $this->_orderCollectionFactory  = $orderCollectionFactory;
        $this->_order                   = $order;
        $this->_date                    = $date;
        $this->_storeTime               = $storeTime;
        $this->_invoiceOrder            = $invoiceOrder;
        $this->_dir                     = $dir;
        $this->_file                    = $file;
        $this->_storeManager            = $storeManager;
        $this->_scopeConfig             = $scopeConfig;
        $this->_customer                = $customerRepositoryInterface;
        $this->_exportFactory           = $exportFactory;
        $this->_objectManager           = $objectManager;
    }

    /**
     * Execute the cron
     *
     * @return bool
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     * @throws \PHPExcel_Writer_Exception
     */
    public function execute($schedule)
    {
        $jobCode = $schedule->getJobCode();
        preg_match('/_id_([0-9]+)/', $jobCode, $matches);
        $this->logger->info('matches', ['matches' => $matches]);
        if (isset($matches[1]) && (int)$matches[1] > 0) {
            $jobId  = (int)$matches[1];

            $dataExport         = $this->_exportFactory->create()->load($jobId)->getData();
            $dataDateConfig     = json_decode($dataExport['date_config'], true);
            $dataExportSource   = json_decode($dataExport['export_source'], true);

            $this->logger->info('JobId', ['jobid' => $jobId]);
            $this->logger->info('Export', ['export' => $jobCode]);
            $this->logger->info('dataExport', ['dataExport' => $dataExport]);

            // create invoices excel file
            $objPHPExcel    = new \PHPExcel();

            $styleHeadBlue = [
                'fill' => [
                    'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => ['rgb' => '9DC3E6']
                ]];

            $styleHeadSollidYellow = [
                'fill' => [
                    'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => ['rgb' => 'FFC000']
                ]];

            $styleAlignLeft = [
                'alignment' => [
                    'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_LEFT
                ]
            ];

            $objPHPExcel->getActiveSheet()->getStyle('A1:M1')->applyFromArray($styleHeadBlue);
            $objPHPExcel->getActiveSheet()->getStyle('E1')->applyFromArray($styleHeadSollidYellow);
            $objPHPExcel->getActiveSheet()->getStyle('N1')->applyFromArray($styleHeadSollidYellow);
            $objPHPExcel->getActiveSheet()->getStyle('O1')->applyFromArray($styleHeadSollidYellow);
            $objPHPExcel->getActiveSheet()->getStyle('A')->applyFromArray($styleAlignLeft);

            $objPHPExcel->getProperties()->setCreator("Marvelic")
                ->setLastModifiedBy("Marvelic")
                ->setTitle("PHPExcel Test Document")
                ->setSubject("PHPExcel Test Document")
                ->setDescription("Generated using PHP classes.")
                ->setKeywords("office PHPExcel php")
                ->setCategory("Result file");

            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', 'Shop Id')
                ->setCellValue('B1', 'Consignee Name')
                ->setCellValue('C1', 'Address Line 1')
                ->setCellValue('D1', 'Province')
                ->setCellValue('E1', 'District')
                ->setCellValue('F1', 'Sub District')
                ->setCellValue('G1', 'Postcode')
                ->setCellValue('H1', 'Email')
                ->setCellValue('I1', 'Tel')
                ->setCellValue('J1', 'Delivery Mode')
                ->setCellValue('K1', 'Note')
                ->setCellValue('L1', 'Sale Agent Code')
                ->setCellValue('M1', 'Customer Ref')
                ->setCellValue('N1', 'Order Number')
                ->setCellValue('O1', 'Order ID')
                ->setCellValue('P1', 'SMS')
                ->setCellValue('Q1', 'Billing Title')
                ->setCellValue('R1', 'Delivery Date')
                ->setCellValue('S1', 'Create Date Time')
                ->setCellValue('T1', 'Payment Method')
                ->setCellValue('U1', 'Payment Date')
                ->setCellValue('V1', 'Payment Time')
                ->setCellValue('W1', 'Payment Amount')
                ->setCellValue('X1', 'Urgent')
                ->setCellValue('Y1', 'Status')
                ->setCellValue('Z1', 'Service Id 1')
                ->setCellValue('AA1', 'Service Id 2')
                ->setCellValue('AB1', 'Service Id 3')
                ->setCellValue('AC1', 'Item Id')
                ->setCellValue('AD1', 'Amount')
                ->setCellValue('AE1', 'Price')
                ->setCellValue('AF1', 'Item Type');

            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(22);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(22);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(22);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(22);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('W')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('X')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Y')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Z')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AA')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AB')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AC')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AD')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AE')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AF')->setWidth(25);

            $periodTime = $dataDateConfig['period'];
            $typeTime   = $dataDateConfig['type_time'];
            $to         = date("Y:m:d h:i:s"); // current date
            $from       = strtotime('-' . $periodTime . ' ' . $typeTime, strtotime($to));
            $from       = date('Y:m:d h:i:s', $from); // days before
            $invoices   = $this->_invoiceOrder->create()
                ->addAttributeToSelect('*')
                ->addFieldToFilter('created_at', ['from'=>$from, 'to'=>$to]);
            $ordColA    = ord('A');
            $row        = 2;
            $dataExcel  = [
                'shopId'                => '',
                'consigneeName'         => '',
                'addressLine1'          => '',
                'province'              => '',
                'district'              => '',
                'subDistrict'           => '',
                'postcode'              => '',
                'email'                 => '',
                'tel'                   => '',
                'deliveryMode'          => '',
                'note'                  => '',
                'saleAgentCode'         => '',
                'customerRef'           => '',
                'orderNumber'           => '',
                'orderID'               => '',
                'sms'                   => '',
                'billingTitle'          => '',
                'deliveryDate'          => '',
                'createDateTime'        => '',
                'paymentMethod'         => '',
                'paymentDate'           => '',
                'paymentTime'           => '',
                'paymentAmount'         => '',
                'urgent'                => '',
                'status'                => '',
                'serviceId1'            => '',
                'serviceId2'            => '',
                'serviceId2'            => '',
                'serviceId3'            => '',
                'itemId'                => '',
                'amount'                => '',
                'price'                 => '',
                'itemType'              => ''
            ];
            foreach ($invoices as $key => $invoice) {
                $order      = $this->_order->load($invoice->getOrderId());
                $customerId = $order->getCustomerId();

                if ($customerId) {
                    $objAddresss    = $this->_customer->getById($customerId)->getAddresses()[0];
                    $tumbon         = $objAddresss->getCustomAttribute('tumbon')
                        ? $objAddresss->getCustomAttribute('tumbon')->getValue()
                        : 'None';

                    $province       = $objAddresss->getCustomAttribute('province')
                        ? $objAddresss->getCustomAttribute('province')->getValue()
                        : 'None';
                    $customerCardId = ($this->_customer->getById($customerId)->getCustomAttribute('personal_id'))
                        ? $this->_customer->getById($customerId)->getCustomAttribute('personal_id')
                        : 'None';
                } else {
                    $tumbon         = 'None';
                    $province       = 'None';
                    $customerCardId = 'None';
                }
                $shipment = $invoice->getData();
                $time = strtotime($shipment["created_at"]);

                $dataExcel['shopId']                = $order->getStoreId();
                $dataExcel['consigneeName']         = $order->getShippingAddress()->getFirstName() . ' ' . $order->getShippingAddress()->getLastName();
                $dataExcel['addressLine1']          = $order->getShippingAddress()->getStreet()[0];
                $dataExcel['province']              = $order->getShippingAddress()->getProvince();
                $dataExcel['district']              = $order->getShippingAddress()->getDistrict();
                $dataExcel['subDistrict']           = $order->getShippingAddress()->getSubDistrict();
                $dataExcel['postcode']              = $order->getShippingAddress()->getPostCode();
                $dataExcel['email']                 = $order->getShippingAddress()->getEmail();
                $dataExcel['tel']                   = $order->getShippingAddress()->getTelephone();
                $dataExcel['deliveryMode']          = "";
                $dataExcel['note']                  = $order->getCustomerNote();
                $dataExcel['saleAgentCode']         = "";
                $dataExcel['customerRef']           = "";
                $dataExcel['orderNumber']           = $key;
                $dataExcel['orderID']               = $order->getId();
                $dataExcel['sms']                   = $order->getStoreId();
                $dataExcel['billingTitle']          = $order->getBillingAddress()->getFirstName();
                $dataExcel['deliveryDate']          = date('Y-m-d', $time);
                $dataExcel['createDateTime']        = date('Y-m-d H:i:s', $time);
                $dataExcel['paymentMethod']         = $order->getPayment()->getMethod();
                $dataExcel['paymentDate']           = date('Y-m-d', $time);
                $dataExcel['paymentTime']           = date('H:i:s', $time);
                $dataExcel['paymentAmount']         = $order->getPayment()->getBaseAmountPaid();
                $dataExcel['urgent']                = "XXX";
                $dataExcel['status']                = $order->getStatus();
                $dataExcel['serviceId1']            = "0";
                $dataExcel['serviceId2']            = "0";
                $dataExcel['serviceId3']            = "0";

                $str = "";
                foreach ($order->getAllItems() as $item) {
                    $dataExcel['itemId']        = $item->getProductId();
                    $dataExcel['amount']        = $item->getQtyOrdered();
                    $dataExcel['price']         = $item->getPrice();
                    $dataExcel['itemType']        = $item->getProductType();

                    $colPosition                = 0;
                    $alph = 0;
                    foreach ($dataExcel as $key => $data) {
                        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($colPosition, $row, $data);
                        $colPosition++;
                        $str .= $data;
                    }
                    $row++;
                }
            }

            $io = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $fileCreatedAt = date("F j, Y h:i:s A", strtotime('+7 hours', strtotime($to)));
        }

        // Upload export file
        if ($dataExportSource['type'] == 'file') {
            if (!file_exists($this->_dir->getPath('media') . '/' . $dataExportSource['file_path'])) {
                $this->_file->mkdir($this->_dir->getPath('media') . '/' . $dataExportSource['file_path'], 0777);
            }

            $pathSave = $this->_dir->getPath('media') . '/' . $dataExportSource['file_path'] . "/" . $dataExport['title'] . "-" . $fileCreatedAt . '.xlsx';
            $io->save($pathSave);
        } else {
            if (!file_exists($this->_dir->getPath('media') . '/tmpExport')) {
                $this->_file->mkdir($this->_dir->getPath('media') . '/tmpExport', 0777);
            }
            $pathSave = $this->_dir->getPath('media') . '/tmpExport/' . $dataExport['title'] . '-' . $fileCreatedAt . '.xlsx';

            // Upload export SFTP and FTP
            if ($dataExportSource['type'] == 'sftp') {
                $client = $this->_objectManager->create('Marvelic\Job\Model\Source\Type\Sftp');
                $argsConfig = [
                    'host'          => $dataExportSource['host'],
                    'port'          => $dataExportSource['port'],
                    'username'      => $dataExportSource['username'],
                    'password'      => $dataExportSource['password'],
                    'file_path'     => $dataExportSource['file_path'],
                    'file_source'   => $pathSave
                ];

                $io->save($pathSave);
                $client->run($argsConfig);
            } elseif ($dataExportSource['type'] == 'ftp') {
                $client = $this->_objectManager->create('Marvelic\Job\Model\Source\Type\Ftp');
                $argsConfig = [
                    'host'          => $dataExportSource['host'],
                    'port'          => $dataExportSource['port'],
                    'user'          => $dataExportSource['username'],
                    'password'      => $dataExportSource['password'],
                    'file_path'     => $dataExportSource['file_path'],
                    'file_source'   => $pathSave
                ];

                $io->save($pathSave);
                $client->run($argsConfig);
            }

            // Clear tmp file
            $this->_file->rm($pathSave);

            return true;
        }

        return false;
    }
}
