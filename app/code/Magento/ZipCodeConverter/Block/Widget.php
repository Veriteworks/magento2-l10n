<?php
namespace Magento\ZipCodeConverter\Block;

use \Magento\Framework\View\Element\Template\Context;
use \Magento\ZipCodeConverter\Model\Config\ConverterConfig;
use \Magento\Framework\App\Cache\Type\Config as CacheConfig;
use \Magento\Directory\Model\ResourceModel\Country\CollectionFactory;
use \Magento\Directory\Model\ResourceModel\Country\Collection;

class Widget extends \Magento\Directory\Block\Data
{

    protected $_template = 'Magento_ZipCodeConverter::widget.phtml';

    private $services;

    private $converterConfig;

    private $cacheConfig;

    private $collectionFactory;

    /**
     * Widget constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\App\Cache\Type\Config $configCacheType $configCacheType
     * @param CollectionFactory $collectionFactory
     * @param ConverterConfig $converterConfig
     * @param array $services
     * @param array $data
     */
    public function __construct(
        Context $context,
        CacheConfig $configCacheType,
        CollectionFactory $collectionFactory,
        ConverterConfig $converterConfig,
        array $services = [],
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->converterConfig = $converterConfig;
        $this->cacheConfig = $configCacheType;
        $this->services = $services;
        $this->collectionFactory = $collectionFactory;
    }

    public function getCountryBasedConfiguration()
    {
        $config = [];
        /** @var Collection $countryCollection */
        $countryCollection = $this->getCountryCollection();
        /** @var \Magento\Directory\Model\Country $country */
        foreach($countryCollection as $country) {
            $countryId = $country->getCountryId();
            $countryConfig =  $this->converterConfig->getConfiguration($countryId);
            if(!is_null($countryConfig)) {
                $config[$country->getCountryId()] = $countryConfig;
            } else {
                $config[$country->getCountryId()] = $this->converterConfig->getDefaultConfiguration();
            }
        }

        return json_encode($config);
    }

}