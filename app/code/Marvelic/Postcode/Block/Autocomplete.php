<?php 
namespace Marvelic\Postcode\Block;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Autocomplete extends \Magento\Framework\View\Element\Template
{
	protected $scopeConfig;

	public function __construct(\Magento\Framework\View\Element\Template\Context $context, ScopeConfigInterface $scopeConfig)
	{
		$this->scopeConfig = $scopeConfig;
		parent::__construct($context);
	}

	public function getConfig(){
		return $this->scopeConfig->getValue('postcode/options/enable',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}
}
