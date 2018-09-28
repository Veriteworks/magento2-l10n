<?php
namespace Magento\ZipCodeConverter\Block;

use \Magento\Directory\Model\ResourceModel\Country\Collection;

class Widget extends \Magento\Directory\Block\Data
{

    /**
     * @var string
     */
    protected $_template = 'Magento_ZipCodeConverter::widget.phtml';

    /**
     * @var array
     */
    private $services;

    /**
     * @var \Magento\ZipCodeConverter\Model\Config\ConverterConfig
     */
    private $converterConfig;

    /**
     * Widget constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Directory\Helper\Data $directoryHelper
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Framework\App\Cache\Type\Config $configCacheType
     * @param \Magento\Directory\Model\ResourceModel\Region\CollectionFactory $regionCollectionFactory
     * @param \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $collectionFactory
     * @param \Magento\ZipCodeConverter\Model\Config\ConverterConfig $converterConfig
     * @param array $services
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Directory\Helper\Data $directoryHelper,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\App\Cache\Type\Config $configCacheType,
        \Magento\Directory\Model\ResourceModel\Region\CollectionFactory $regionCollectionFactory,
        \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $collectionFactory,
        \Magento\ZipCodeConverter\Model\Config\ConverterConfig $converterConfig,
        array $services = [],
        array $data = []
    ) {
        parent::__construct($context,
                            $directoryHelper,
                            $jsonEncoder,
                            $configCacheType,
                            $regionCollectionFactory,
                            $collectionFactory,
                            $data
        );
        $this->converterConfig = $converterConfig;
        $this->services = $services;
    }

    /**
     * @return false|string
     */
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
                $service = $countryConfig['model'];
                $credential = $this->services[$service]->getCredential();
                $countryConfig = $countryConfig + $credential;
                $config[$country->getCountryId()] = $countryConfig;
            } else {
                $credential = $this->services['googleapi']->getCredential();
                $config['default'] = $this->converterConfig->getDefaultConfiguration() + $credential;
            }
        }

        return json_encode($config);
    }



}