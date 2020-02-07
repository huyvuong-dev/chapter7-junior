<?php
namespace Magenest\Chapter7\Observer\Cart;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;

class SetAdditionalOptions implements ObserverInterface
{
    /**
     * @var RequestInterface
     */
    protected $_request;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    protected $_productMetadata;

    public function __construct(
        RequestInterface $request,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata
    )
    {
        $this->_productMetadata = $productMetadata;
        $this->date = $date;
        $this->_request = $request;

    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $product = $observer->getProduct();
        $additionalOptions = [];
        $additionalOptions[] = array(
            'label' => __("Time Stamp"), //Custom option label
            'value' => $this->date->timestamp(), //Custom option value
        );
        $version = $this->_productMetadata->getVersion();
        if (version_compare($version,'2.2.0') >= 0){
            $objectManager = ObjectManager::getInstance();
            $serializer = $objectManager->create('\Magento\Framework\Serialize\Serializer\Json');
            $product->addCustomOption('additional_options', $serializer->serialize($additionalOptions));
            $quoteItem = $observer->getData('quote_item');
            $quoteItem->addOption($product->getCustomOption('additional_options'));
        }else {
            $product->addCustomOption('additional_options', serialize($additionalOptions));
            $quoteItem = $observer->getData('quote_item');
            $quoteItem->addOption($product->getCustomOption('additional_options'));
        }
    }

}