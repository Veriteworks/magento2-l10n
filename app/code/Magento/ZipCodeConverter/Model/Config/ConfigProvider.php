<?php
namespace Magento\ZipCodeConverter\Model\Config;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\ZipCodeConverter\Model\Config\ConverterConfig;

class CvsProvider implements ConfigProviderInterface
{
    private $converterConfig;


    public function __construct(
        ConverterConfig $converterConfig
    )
    {
        $this->converterConfig = $converterConfig;

    }

    public function getConfig()
    {
        //todo: retrieve country based configuration by allowed country code.
    }

}