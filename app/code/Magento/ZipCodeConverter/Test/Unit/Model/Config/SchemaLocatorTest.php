<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\ZipCodeConverter\Test\Unit\Model\Config;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;

class SchemaLocatorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $moduleReaderMock;

    /**
     * @var \Magento\ZipCodeConverter\Model\Config\SchemaLocator
     */
    protected $model;

    /**
     * Set up test environment.
     *
     * @return void
     */
    protected function setUp()
    {
        $this->moduleReaderMock = $this->createMock(\Magento\Framework\Module\Dir\Reader::class);
        $this->moduleReaderMock->expects(
            $this->any()
        )->method(
            'getModuleDir'
        )->with(
            'etc',
            'Magento_ZipCodeConverter'
        )->will(
            $this->returnValue('schema_dir')
        );

        $objectManager = new ObjectManagerHelper($this);
        $this->model = $objectManager->getObject(
            \Magento\ZipCodeConverter\Model\Config\SchemaLocator::class,
            [
                'moduleReader' => $this->moduleReaderMock
            ]
        );
    }

    /**
     * Test getSchema() method.
     */
    public function testGetSchema()
    {
        $this->assertEquals('schema_dir' . DIRECTORY_SEPARATOR . 'zipconverter.xsd', $this->model->getSchema());
    }

    /**
     * Test getPerFileSchema() method.
     */
    public function testGetPerFileSchema()
    {
        $this->assertEquals('schema_dir' . DIRECTORY_SEPARATOR . 'zipconverter.xsd', $this->model->getPerFileSchema());
    }
}
