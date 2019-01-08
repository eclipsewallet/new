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

    public function __construct(\Psr\Log\LoggerInterface $logger,
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
    )
    {
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
     * @return void
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
            $objPHPExcel    = new \PHPExcel;

            $styleHeadBlue = array(
                'fill' => array(
                    'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => '9DC3E6')
                ));

            $styleHeadSollidYellow = array(
                'fill' => array(
                    'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'FFC000')
                ));

            $styleYellow = array(
                'fill' => array(
                    'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'FFE699')
                ));

            $styleLightYellow = array(
                'fill' => array(
                    'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'FFF2CC')
                ));

            $styleAlignLeft = array(
                'alignment' => array(
                    'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_LEFT
                )
            );

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
                ->setCellValue('A1', 'Sale id')
                ->setCellValue('B1', 'Sale date')
                ->setCellValue('C1', 'Product id')
                ->setCellValue('D1', 'Product title')
                ->setCellValue('E1', 'Sale quatity')
                ->setCellValue('F1', 'Customer id card')
                ->setCellValue('G1', 'Shipping address to')
                ->setCellValue('H1', 'Shipping address name')
                ->setCellValue('I1', 'Shipping tombol name')
                ->setCellValue('J1', 'Shipping amphur name')
                ->setCellValue('K1', 'Shipping province name')
                ->setCellValue('L1', 'Shipping postcode')
                ->setCellValue('M1', 'Shipping phone')
                ->setCellValue('N1', 'ประเภทการจัดส่ง (Delivery type)')
                ->setCellValue('O1', 'ปณ.ที่รับสินค้า (PostCode Name)');

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

            $periodTime = $dataDateConfig['period'];
            $typeTime   = $dataDateConfig['type_time'];
            $to         = date("Y:m:d h:i:s"); // current date
            $from       = strtotime('-'.$periodTime.' '.$typeTime, strtotime($to));
            $from       = date('Y:m:d h:i:s', $from); // days before
            $invoices   = $this->_invoiceOrder->create()
                ->addAttributeToSelect('*')
                ->addFieldToFilter('created_at', array('from'=>$from, 'to'=>$to));
            $ordColA    = ord('A');
            $row        = 2;
            $dataExcel  = [
                'orderIncrementId'      => '',
                'saleDate'              => '',
                'productId'             => '',
                'productTitle'          => '',
                'saleQuantity'          => '',
                'customerCardId'        => '',
                'shippingAddressTo'     => '',
                'shippingAddressName'   => '',
                'shippingTumbolName'    => '',
                'shippingAmphurName'    => '',
                'shippingProvinceName'  => '',
                'shippingPostcode'      => '',
                'shippingPhone'         => '',
                'deliveryType'          => '',
                'postCodeName'          => ''
            ];

            foreach ($invoices as $invoice) {
                $order      = $this->_order->load($invoice->getOrderId());
                $customerId = $order->getCustomerId();

                if ($customerId){
                    $objAddresss    = $this->_customer->getById($customerId)->getAddresses()[0];
                    $tumbon         = $objAddresss->getCustomAttribute('tumbon')
                        ? $objAddresss->getCustomAttribute('tumbon')->getValue()
                        : 'None';

                    $province       = $objAddresss->getCustomAttribute('province')
                        ? $objAddresss->getCustomAttribute('province')->getValue()
                        : 'None';
                    $customerCardId = ($this->_customer->getById($customerId)->getCustomAttribute('personal_id'))
                        ?  $this->_customer->getById($customerId)->getCustomAttribute('personal_id')
                        : 'None';
                }else{
                    $tumbon         = 'None';
                    $province       = 'None';
                    $customerCardId = 'None';
                }

                $dataExcel['orderIncrementId']      = $order->getIncrementId();
                $dataExcel['saleDate']              = date("Y/m/d h:i:s A", strtotime('+7 hours', strtotime($order->getCreatedAt())));
                $dataExcel['shippingAddressTo']     = $order->getShippingAddress()->getFirstName().' '
                    .$order->getShippingAddress()->getLastName();
                $dataExcel['shippingAddressName']   = $order->getShippingAddress()->getStreet()[0].' '
                    .$order->getShippingAddress()->getCity();
                $dataExcel['shippingTumbolName']    = $tumbon;
                $dataExcel['shippingProvinceName']  = $province;
                $dataExcel['shippingAmphurName']    = $order->getShippingAddress()->getCity();
                $dataExcel['shippingPostcode']      = $order->getShippingAddress()->getPostcode();
                $dataExcel['shippingPhone']         = $order->getShippingAddress()->getTelephone();
                $dataExcel['customerCardId']        = $customerCardId;

                foreach ($order->getAllItems() as $item) {
                    $dataExcel['productId']     = $item->getProductId();
                    $dataExcel['productTitle']  = $item->getName();
                    $dataExcel['saleQuantity']  = $item->getQtyOrdered();
                    $colPosition                = $ordColA;

                    foreach ($dataExcel as $key => $data) {
                        if ($key === 'saleQuantity' || $key === 'customerCardId' || $key === 'deliveryType'){
                            $objPHPExcel->getActiveSheet()->getStyle(chr($colPosition).$row)->applyFromArray($styleLightYellow);
                        }
                        if ($key === 'postCodeName'){
                            $objPHPExcel->getActiveSheet()->getStyle(chr($colPosition).$row)->applyFromArray($styleYellow);
                        }
                        $objPHPExcel->getActiveSheet()->SetCellValue(chr($colPosition).$row, $data);
                        $colPosition++;
                    }

                    $row++;
                }
            }

            $io = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

            if ( !file_exists($this->_dir->getPath('media').'/ktb')) {
                $this->_file->mkdir($this->_dir->getPath('media').'/ktb', 0777);
            }

            $fileCreatedAt = date("F j, Y h:i:s A", strtotime('+7 hours', strtotime($to)));
            $pathSave = $this->_dir->getPath('media').'/ktb/'.$dataExport['title']."-".$fileCreatedAt.'.xlsx';
            $io->save($pathSave);

            // Upload SFTP
            if ($dataExportSource['type'] == 'sftp'){
                $client = $this->_objectManager->create('Marvelic\Job\Model\Source\Type\Sftp');
                $argsConfig = [
                    'host'          => $dataExportSource['host'],
                    'port'          => $dataExportSource['port'],
                    'username'      => $dataExportSource['username'],
                    'password'      => $dataExportSource['password'],
                    'file_path'     => $dataExportSource['file_path'],
                    'file_source'   => $pathSave
                ];

                $this->logger->info('argsConfig', ['argsConfig' => $argsConfig]);

                $client->run($argsConfig);
            }

            return true;
        }

        return false;


    }
}
