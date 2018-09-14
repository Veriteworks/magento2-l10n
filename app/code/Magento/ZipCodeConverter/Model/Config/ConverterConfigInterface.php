<?php
namespace Magento\ZipCodeConverter\Model\Config;

interface ConverterConfigInterface
{
    /**
     * Retrieve country specific configuration as array
     *
     * @param $iso2CountryCode
     * @return array|null
     */
    public function getConfiguration($iso2CountryCode);

    /**
     * Retrieve default configuration as array
     *
     * @return array
     */
    public function getDefaultConfiguration();
}