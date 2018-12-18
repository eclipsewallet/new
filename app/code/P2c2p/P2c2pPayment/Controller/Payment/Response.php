<?php
/*
 * Created by 2C2P
 * Date 20 June 2017
 * This Response action method is responsible for handle the 2c2p payment gateway response.
 */

namespace P2c2p\P2c2pPayment\Controller\Payment;

use Magento\Checkout\Model\Session;
use Magento\Customer\Model\Session as Customer;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderFactory;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order\Payment\Transaction;
use Omise\Payment\Model\Config\Offsite\Internetbanking as Config;
use P2c2p\P2c2pPayment\Helper\Checkout;
use P2c2p\P2c2pPayment\Helper\P2c2pHash;
use P2c2p\P2c2pPayment\Helper\P2c2pMeta;
use P2c2p\P2c2pPayment\Helper\P2c2pRequest;


class Response extends \P2c2p\P2c2pPayment\Controller\AbstractCheckoutRedirectAction {

	/**
	 * @var string
	 */
	const PATH_CART = 'checkout/cart';
	const PATH_SUCCESS = 'checkout/onepage/success';

	/**
	 * @var \Magento\Checkout\Model\Session
	 */
	protected $session;

	/**
	 * @var \Omise\Payment\Model\Config\Offsite\Internetbanking
	 */
	protected $config;
    protected $orderSender;
    protected $orderCommentSender;
    protected $invoiceSender;
    protected $_transaction;
    protected $_invoiceService;
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
        \Magento\Sales\Model\Order\Email\Sender\OrderSender $orderSender,
        \Magento\Sales\Model\Order\Email\Sender\OrderCommentSender $orderCommentSender,
        \Magento\Framework\DB\Transaction $transaction,
        \Magento\Sales\Model\Order\Payment\Transaction\BuilderInterface $transactionBuilder,
        \Magento\Sales\Model\Service\InvoiceService $invoiceService
	) {
		parent::__construct($context, $session, $orderFactory, $customer, $checkoutHelper, $p2c2pRequest, $p2c2pMeta, $p2c2pHash, $configSettings, $catalogSession);
		$this->session = $session;
        $this->orderSender = $orderSender;
        $this->orderCommentSender = $orderCommentSender;
        $this->_transaction = $transaction;
        $this->transactionBuilder = $transactionBuilder;
        $this->invoiceSender = $this->_objectManager->get('Magento\Sales\Model\Order\Email\Sender\InvoiceSender');
        $this->_invoiceService = $invoiceService;
	}

	public function execute() {
		//If payment getway response is empty then redirect to home page directory.
		if (empty($_REQUEST) || empty($_REQUEST['order_id'])) {
			$this->messageManager->addSuccess(__('Someting is wrong with your order!'));
			$this->_redirect('');
			return;
		}

		//Extract the Payment getaway resposne object.
		extract($_REQUEST, EXTR_OVERWRITE); // Extract the response from 2c2p.

		$hashHelper = $this->getHashHelper();
		$configHelper = $this->getConfigSettings();
		$objCustomerData = $this->getCustomerSession();

		$secretKey = array_key_exists("secretKey", $configHelper) ? $configHelper['secretKey'] : '';
		$isValidHash = $hashHelper->isValidHashValue($_REQUEST, $secretKey);

		//Get Payment getway response to variable.
		$payment_status_code = $_REQUEST['payment_status'];
		$transaction_ref 	 = $_REQUEST['transaction_ref']; 
		$approval_code   	 = $_REQUEST['approval_code'];
		$payment_status  	 = $_REQUEST['payment_status'];
		$order_id 		 	 = $_REQUEST['order_id'];

		//Get the object of current order.
		$order = $this->getOrderDetailByOrderId($order_id);

		$order = $this->session->getLastRealOrder();

		//Check whether hash value is valid or not If not valid then redirect to home page when hash value is wrong.
		if (!$isValidHash) {
			$order->setState(\Magento\Sales\Model\Order::STATUS_FRAUD);
			$order->setStatus(\Magento\Sales\Model\Order::STATUS_FRAUD);
			$order->save();

			$this->messageManager->addSuccess(__('Someting is wrong with your order!'));

			$this->_redirect('');
			return;
		}
		//check payment status according to payment response.
		if (strcasecmp($payment_status, "000") == 0) {
			//IF payment status code is success	
			// Update order state and status.
			$order->setState(\Magento\Sales\Model\Order::STATE_PROCESSING);
			$order->setStatus(\Magento\Sales\Model\Order::STATE_PROCESSING);
			//Set the complete status when payment is completed.
			$order->save();
			$this->executeSuccessAction($_REQUEST);
			return;

		} else if (strcasecmp($payment_status, "001") == 0) {
			//Set the Pending payment status when payment is pending. like 123 payment type.
			$order->setState("Pending_2C2P");
			$order->setStatus("Pending_2C2P");

			$order->save();

			$this->executeSuccessAction($_REQUEST);
			return;

		} else if (strcasecmp($payment_status, "002") == 0) {
			//002 = Rejected (Failed payment).
			$this->messageManager->addSuccess(__($channel_response_desc));
			$this->executeCancelAction();
            $this->_eventManager->dispatch('order_cancel_after', ['order' => $order]);
            $strComment = "Order has been cancelled with P2c2p code " . $payment_status . " " .$channel_response_desc ;

            $order->addStatusHistoryComment($strComment)->setEntityName('order')->save();
            $this->orderCommentSender->send($order, true, $strComment);
			return;
		} else {
			//If payment status code is cancel/Error/other.
			$this->messageManager->addSuccess(__($channel_response_desc));
			$this->executeCancelAction();
            $this->_eventManager->dispatch('order_cancel_after', ['order' => $order]);
            $strComment = "Order has been cancelled with P2c2p code " . $payment_status . " " . $channel_response_desc;

            $order->addStatusHistoryComment($strComment)->setEntityName('order')->save();
            $this->orderCommentSender->send($order, true, $strComment);
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