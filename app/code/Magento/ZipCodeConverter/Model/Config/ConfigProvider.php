<?php
namespace Magento\ZipCodeConverter\Model\Config;

use \Magento\Checkout\Model\ConfigProviderInterface;
use \Magento\Directory\Model\ResourceModel\Country\Collection;

class ConfigProvider implements ConfigProviderInterface
{
    /**
     * @var ConverterConfig
     */
    private $converterConfig;

    /**
     * @var \Magento\Directory\Model\ResourceModel\Region\CollectionFactory
     */
    private $regionCollectionFactory;

    /**
     * @var \Magento\Directory\Model\ResourceModel\Country\CollectionFactory
     */
    private $countryCollectionFactory;

    /**
     * @var
     */
    private $countryCollection;

    /**
     * @var array
     */
    private $services = [];

    public function __construct(
        ConverterConfig $converterConfig,
        \Magento\Directory\Model\ResourceModel\Region\CollectionFactory $regionCollectionFactory,
        \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory
    ) {
        $this->converterConfig = $converterConfig;
        $this->regionCollectionFactory = $regionCollectionFactory;
        $this->countryCollectionFactory = $countryCollectionFactory;
    }

    public function getConfig()
    {
        $config = [];
        /** @var Collection $countryCollection */
        $countryCollection = $this->getCountryCollection();

        /** @var \Magento\Directory\Model\Country $country */
        foreach ($countryCollection as $country) {
            $countryId = $country->getCountryId();
            $countryConfig = $this->converterConfig->getConfiguration($countryId);

            if (!is_null($countryConfig)) {
                $service = $countryConfig['model'];
                $credential = $this->services[$service]->getCredential();
                $countryConfig = $countryConfig + $credential;
                $config[$country->getCountryId()] = $countryConfig;
            } else {
                $credential = $this->services['googleapi']->getCredential();
                $config['default'] = $this->converterConfig->getDefaultConfiguration() + $credential;
            }
        }

        return $config;
    }


    private function getCountryCollection()
    {
        $collection = $this->countryCollection;
        if ($collection === null) {
            $collection = $this->countryCollectionFactory->create()->loadByStore();
            $this->countryCollection = $collection;
        }

        return $this->getCountryCollection();
    }

}