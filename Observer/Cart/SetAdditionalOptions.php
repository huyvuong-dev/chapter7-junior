<?php
namespace Magenest\Chapter7\Observer\Cart;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Serialize\Serializer\Json;

class SetAdditionalOptions implements ObserverInterface
{
    /**
     * @var RequestInterface
     */
    protected $_request;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $serializer;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    public function __construct(
        RequestInterface $request,
        Json $serializer = null,
        \Magento\Framework\Stdlib\DateTime\DateTime $date
    )
    {
        $this->date = $date;
        $this->_request = $request;
        $this->serializer = $serializer ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magento\Framework\Serialize\Serializer\Json::class);
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        // Check and set information according to your need
        $product = $observer->getProduct();
        if ($this->_request->getFullActionName() == 'checkout_cart_add') { //checking when product is adding to cart
            $product = $observer->getProduct();
            $additionalOptions = [];
            $additionalOptions[] = array(
                'label' => __("Time Stamp"), //Custom option label
                'value' => $this->date->timestamp(), //Custom option value
            );
            $product->addCustomOption('additional_options', $this->serializer->serialize($additionalOptions));
        }
    }

}