<?php
namespace Magento\ZipCodeConverter\Model\Service;

use \Magento\Store\Model\ScopeInterface;

class Google implements ConverterInterface
{
    const CODE = 'googleapi';

    const XML_PATH = 'zipcode/google/key';

    private $scopeConfig;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function getCredential()
    {
        $value = $this->scopeConfig->getValue(self::XML_PATH, ScopeInterface::SCOPE_STORE);

        return [
                    'credentials' => ['key' => $value]
                ];
    }
}