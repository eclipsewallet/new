<?php

namespace P2c2p\P2c2pPayment\Controller\Payment;
use Psr\Log\LoggerInterface;

class Notify2C2P extends \P2c2p\P2c2pPayment\Controller\AbstractCheckoutRedirectAction
{
	protected $_logger;
    protected $_invoiceService;
    protected $invoiceSender;
    protected $_transaction;
    protected $transactionBuilder;
	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Checkout\Model\Session $session,
		\Magento\Sales\Model\OrderFactory $orderFactory,
		\Magento\Customer\Model\Session $customer,
		\P2c2p\P2c2pPayment\Helper\Checkout $checkoutHelper,
		\P2c2p\P2c2pPayment\Helper\P2c2pRequest $p2c2pRequest,
		\P2c2p\P2c2pPayment\Helper\P2c2pMeta $p2c2pMeta,
		\P2c2p\P2c2pPayment\Helper\P2c2pHash $p2c2pHash,
		\Magento\Framework\App\Config\ScopeConfigInterface $configSettings,
		\Magento\Catalog\Model\Session $catalogSession,
		LoggerInterface $logger,
        \Magento\Sales\Model\Service\InvoiceService $invoiceService,
        \Magento\Framework\DB\Transaction $transaction,
        \Magento\Sales\Model\Order\Payment\Transaction\BuilderInterface $transactionBuilder){
		parent::__construct($context, $session, $orderFactory, $customer, $checkoutHelper, $p2c2pRequest, $p2c2pMeta, $p2c2pHash, $configSettings, $catalogSession);
		$this->_logger = $logger;
        $this->_invoiceService = $invoiceService;
        $this->invoiceSender = $this->_objectManager->get('Magento\Sales\Model\Order\Email\Sender\InvoiceSender');
        $this->_transaction = $transaction;
        $this->transactionBuilder = $transactionBuilder;
	}

	public function execute()
	{

		$this->_logger->info('request', ['value' => $_REQUEST]); // write log
		
//		//If payment getway response is empty then redirect to home page directory.
		if(empty($_REQUEST) || empty($_REQUEST['order_id'])){
			$this->_redirect('');
			return;
		}

		$hashHelper   = $this->getHashHelper();
		$configHelper = $this->getConfigSettings();

		$this->_logger->info('secretKey', ['value' => $configHelper['secretKey']]);
		
		$objCustomerData = $this->getCustomerSession();
		$isValidHash  = $hashHelper->isValidHashValue($_REQUEST,$configHelper['secretKey']);
		
		//Get Payment getway response to variable.
		$request_timestamp	 = $_REQUEST['request_timestamp'];
		$merchant_id		 = $_REQUEST['merchant_id'];
		$invoice_no			 = $_REQUEST['invoice_no'];
		$amount 			 = $_REQUEST['amount'];
		$transaction_ref 	 = empty($_REQUEST['transaction_ref']) ? __('None'): $_REQUEST['transaction_ref']; 
		$transaction_datetime 	= $_REQUEST['transaction_datetime'];
		$payment_channel 	 = $_REQUEST['payment_channel'];
		$payment_status_code 	= $_REQUEST['payment_status'];
		$channel_response_code 	= $_REQUEST['channel_response_code'];

		switch ($channel_response_code) {
			case '001':
				$channel_response_code = $channel_response_code.' - Credit and debit cards';
				break;
			case '002':
				$channel_response_code = $channel_response_code.' - Cash payment channel';
				break;
			case '003':
				$channel_response_code = $channel_response_code.' - Direct debit';
				break;
			case '004':
				$channel_response_code = $channel_response_code.' - Others';
				break;
			default:
				$channel_response_code = $channel_response_code.' - IPP transaction';
				break;
		}

		$paid_channel		 = $_REQUEST['paid_channel'];
		$paid_agent 		 = $_REQUEST['paid_agent'];
		$hash_value			 = $_REQUEST['hash_value'];
		$order_id 		 	 = $_REQUEST['order_id'];
		
		//Get the object of current order.
		$order = $this->getOrderDetailByOrderId($order_id);

		//If order is empty then redirect to home page. Because order is not avaialbe.
		if(empty($order)) {
			$this->_redirect('');
			return;
		}

		//Check whether hash value is valid or not If not valid then redirect to home page when hash value is wrong.
		if(!$isValidHash) {
			$this->_logger->info('False hash'); // write log

			$order->setState(\Magento\Sales\Model\Order::STATUS_FRAUD);
			$order->setStatus(\Magento\Sales\Model\Order::STATUS_FRAUD);
			$order->save();

			$this->_redirect('');
			return;
		}else{
			$this->_logger->info('Pass hash'); // write log
		}

		$metaDataHelper = $this->getMetaDataHelper();		
		$metaDataHelper->savePaymentGetawayResponse($_REQUEST,$order->getCustomerId());

		//check payment status according to payment response.
		if(strcasecmp($payment_status_code, "000") == 0) {			
			//IF payment status code is success

			if(!empty($order->getCustomerId()) && !empty($_REQUEST['stored_card_unique_id'])) {
				$intCustomerId = $order->getCustomerId();
				$boolIsFound = false;

				// Fetch data from database by using the customer ID.
				$objTokenData = $metaDataHelper->getUserToken($intCustomerId);
				
				$arrayTokenData = array('user_id' => $intCustomerId,
					'stored_card_unique_id' => $_REQUEST['stored_card_unique_id'],
					'masked_pan' => $_REQUEST['masked_pan'],
					'created_time' =>  date("Y-m-d H:i:s"));

				/* 
				   Iterate foreach and check whether token key is present into p2c2p_token table or not.
				   If token key is already present into database then prevent insert entry otherwise insert token entry into database.
				*/				   
				foreach ($objTokenData as $key => $value) {
					if(strcasecmp($value->getData('masked_pan'), $_REQUEST['masked_pan']) == 0 && 
					   strcasecmp($value->getData('stored_card_unique_id'), $_REQUEST['stored_card_unique_id']) == 0) {
						$boolIsFound = true;
						break;
					}
				}

				if(!$boolIsFound) {
					$metaDataHelper->saveUserToken($arrayTokenData);					
				}
			}

			$payment = $order->getPayment();
			$payment->setTransactionId($transaction_ref);
			$payment->setLastTransId($transaction_ref);

			//Set the complete status when payment is completed.
			$order->setState(\Magento\Sales\Model\Order::STATE_PROCESSING);
			$order->setStatus(\Magento\Sales\Model\Order::STATE_PROCESSING);

			$invoice = $this->invoice($order);
			$invoice->setTransactionId($transaction_ref);

			// Add transaction.
			$payment->addTransactionCommentsToOrder(
				$payment->addTransaction(\Magento\Sales\Model\Order\Payment\Transaction::TYPE_CAPTURE),
				__(
					'Amount of %1 has been paid via 2C2P payment',
					$order->getBaseCurrency()->formatTxt($order->getBaseGrandTotal())
				)
			);
			$order->save();

			$payment_id 	= $payment->getId();
			$order_id		= $order->getId();
			$this->_logger->info('dataId', ['paymentId' => $payment_id, 'orderId' => $order_id]);

			$detailData = [
				\Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS => [
					'Request Timestamp'		=> $request_timestamp,
					'Merchant Id' 			=> $merchant_id,
					'Order Id'				=> $_REQUEST['order_id'],
					'Invoice No'			=> $invoice_no,
					'Amount'				=> $amount,
					'Transaction REF'		=> $transaction_ref,
					'Transaction Datetime'	=> $transaction_datetime,
					'Payment Channel'		=> $payment_channel,
					'Payment Status'		=> '000 - '.__('Payment Successful'),
					'Channel Response Code' => $channel_response_code,
					'Paid Channel'			=> $paid_channel,
					'Paid Agent'			=> $paid_agent,
					'Hash Value'			=> $hash_value
				]
			];

			$detailJson		= json_encode($detailData);

	        $objectManager 	= \Magento\Framework\App\ObjectManager::getInstance();
	        $resource 		= $objectManager->get('Magento\Framework\App\ResourceConnection');
	        $connection 	= $resource->getConnection();
	        $tableName 		= $resource->getTableName('sales_payment_transaction');
	        $sql 			= "Update " . $tableName . " Set additional_information = '".$detailJson."' where order_id = ".$order_id ." and payment_id = '". $payment_id ."'";
        	$connection->query($sql);

            if($configHelper['auto_invoice'] == 1){
                if ($order->canInvoice()) {
                    $invoice = $this->_invoiceService->prepareInvoice($order);
                    $invoice->register();
                    $invoice->save();
                    $transactionSave = $this->_transaction->addObject(
                        $invoice
                    )->addObject(
                        $invoice->getOrder()
                    );
                    $transactionSave->save();
                    $this->invoiceSender->send($invoice);
                    //send notification code
                    $order->addStatusHistoryComment(
                        __('Notified customer about invoice #%1.', $invoice->getId())
                    )
                        ->setIsCustomerNotified(true)
                        ->save();
                }
            }
			$this->executeSuccessAction($_REQUEST);
			return;

		} else if(strcasecmp($payment_status_code, "001") == 0) {			
			//Set the Pending payment status when payment is pending. like 123 payment type.
			$order->setState("Pending_2C2P");
			$order->setStatus("Pending_2C2P");
			$order->save();

			$this->executeSuccessAction($_REQUEST);
			return;

		} else {
			//If payment status code is cancel/Error/other.
			$order->setState(\Magento\Sales\Model\Order::STATE_CANCELED);
			$order->setStatus(\Magento\Sales\Model\Order::STATE_CANCELED);
			$order->save();
			return;
		}
	}

	/**
	 * @param  \Magento\Sales\Model\Order $order
	 *
	 * @return \Magento\Sales\Api\Data\InvoiceInterface
	 */
	protected function invoice(\Magento\Sales\Model\Order $order) {
		return $order->getInvoiceCollection()->getLastItem();
	}
}