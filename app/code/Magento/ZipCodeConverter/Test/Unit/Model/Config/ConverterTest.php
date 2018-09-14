<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\ZipCodeConverter\Test\Unit\Model\Config;

use Magento\ZipCodeConverter\Model\Config\Converter;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;

/**
 * Unit test for Magento\ZipCodeConverter\Model\Config\Converter
 */
class ConverterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Converter
     */
    protected $converter;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $objectManager = new ObjectManagerHelper($this);
        $this->converter = $objectManager->getObject(
            \Magento\ZipCodeConverter\Model\Config\Converter::class
        );
    }

    /**
     * @return void
     */
    public function testConvert()
    {
        $xmlFile = __DIR__ . '/_files/zipconverter_test.xml';
        $dom = new \DOMDocument();
        $dom->loadXML(file_get_contents($xmlFile));
        $result = $this->converter->convert($dom);

        $this->assertInternalType(
            'array',
            $result
        );

        $this->assertArrayHasKey('default', $result);
        $this->assertArrayHasKey('US', $result);
        $this->assertArrayHasKey('JP', $result);
    }
}
