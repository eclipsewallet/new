<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 28/02/2018
 * Time: 14:11
 */

namespace Marvelic\Postcode\Block\Address;


// use Magento\Customer\Model\AttributeChecker;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\App\ObjectManager;

class Edit extends \Magento\Customer\Block\Address\Edit
{
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Directory\Helper\Data $directoryHelper,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\App\Cache\Type\Config $configCacheType,
        \Magento\Directory\Model\ResourceModel\Region\CollectionFactory $regionCollectionFactory,
        \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Api\AddressRepositoryInterface $addressRepository,
        \Magento\Customer\Api\Data\AddressInterfaceFactory $addressDataFactory,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
        array $data = []
        // AttributeChecker $attributeChecker = null
    )
    {
        parent::__construct(
            $context,
            $directoryHelper,
            $jsonEncoder,
            $configCacheType,
            $regionCollectionFactory,
            $countryCollectionFactory,
            $customerSession,
            $addressRepository,
            $addressDataFactory,
            $currentCustomer,
            $dataObjectHelper,
            $data
            // $attributeChecker
        );
    }

    public function getDistrict(){
        $districts = $this->_address->getCustomAttribute('district');
        return $districts === null ? '' : $districts->getValue();
    }
}