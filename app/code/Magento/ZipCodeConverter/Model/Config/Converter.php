<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\ZipCodeConverter\Model\Config;

use Magento\Framework\Config\ConverterInterface;

class Converter implements ConverterInterface
{
    /**
     * {@inheritdoc}
     */
    public function convert($source)
    {
        /** @var \DOMNodeList $converters */
        $converters = $source->getElementsByTagName('converter');
        $result = [];

        foreach ($converters as $converter){
            $country = $converter->getAttribute('countryCode');
            $configuration = [];
            foreach($converter->childNodes as $childNode) {
                if ($childNode->nodeType === XML_ELEMENT_NODE) {
                    $configuration[$childNode->localName] = $childNode->textContent;
                }
            }
            $result[$country] = $configuration;
        }

        return $result;
    }
}
