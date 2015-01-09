<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\ImportExport\Model\Import;

class SourceAbstractTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\ImportExport\Model\Import\AbstractSource|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_model = null;

    protected function setUp()
    {
        $this->_model = $this->getMockForAbstractClass(
            'Magento\ImportExport\Model\Import\AbstractSource',
            [['key1', 'key2', 'key3']]
        );
    }

    /**
     * @param array $argument
     * @dataProvider constructExceptionDataProvider
     * @expectedException \InvalidArgumentException
     */
    public function testConstructException($argument)
    {
        $this->getMockForAbstractClass('Magento\ImportExport\Model\Import\AbstractSource', [$argument]);
    }

    /**
     * @return array
     */
    public function constructExceptionDataProvider()
    {
        return ['empty column names' => [[]], 'duplicate column names' => [['1', '2', '1']]];
    }

    public function testGetColNames()
    {
        $this->assertSame(['key1', 'key2', 'key3'], $this->_model->getColNames());
    }

    public function testIteratorInterface()
    {
        $this->assertSame(['key1' => '', 'key2' => '', 'key3' => ''], $this->_model->current());
        $this->assertSame(-1, $this->_model->key());
        $this->assertFalse($this->_model->valid());

        $this->_model->expects(
            $this->exactly(4)
        )->method(
            '_getNextRow'
        )->will(
            $this->onConsecutiveCalls([1, 2, 3], [4, 5], [6, 7, 8], false)
        );
        $data = [];
        foreach ($this->_model as $key => $value) {
            $data[$key] = $value;
        }
        $this->assertSame(
            [
                ['key1' => 1, 'key2' => 2, 'key3' => 3],
                ['key1' => 4, 'key2' => 5, 'key3' => ''],
                ['key1' => 6, 'key2' => 7, 'key3' => 8],
            ],
            $data
        );
    }

    public function testSeekableInterface()
    {
        $this->assertSame(-1, $this->_model->key());
        $this->_model->seek(-1);
        $this->assertSame(-1, $this->_model->key());

        $this->_model->expects(
            $this->any()
        )->method(
            '_getNextRow'
        )->will(
            $this->onConsecutiveCalls([1, 2, 3], [4, 5], [6, 7, 8], [1, 2, 3], [4, 5])
        );
        $this->_model->seek(2);
        $this->assertSame(['key1' => 6, 'key2' => 7, 'key3' => 8], $this->_model->current());
        $this->_model->seek(1);
        $this->assertSame(['key1' => 4, 'key2' => 5, 'key3' => ''], $this->_model->current());
    }

    /**
     * @expectedException \OutOfBoundsException
     */
    public function testSeekableInterfaceException()
    {
        $this->_model->seek(0);
    }
}
