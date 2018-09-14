<?php
namespace Magento\ZipCodeConverter\Test\Unit\Model\Config;

use Magento\ZipCodeConverter\Model\Config\ConverterConfig;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;

/**
 * Unit test for Magento\ZipCodeConverter\Model\Config\ConverterConfig
 */
class ConverterConfigTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ObjectManagerHelper
     */
    private $objectManager;
    /**
     * @var ConverterConfig
     */
    protected $converterConfig;

    /**
     * @var \Magento\ZipCodeConverter\Model\Config\Reader|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $reader;

    /**
     * @var \Magento\Framework\Config\CacheInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $cache;

    /**
     * @var \Magento\Framework\Serialize\SerializerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $serializerMock;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->objectManager = new ObjectManagerHelper($this);
        $this->reader = $this->getMockBuilder(\Magento\Framework\Config\ReaderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->cache = $this->getMockBuilder(\Magento\Framework\Config\CacheInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->cache->expects($this->any())
            ->method('load')
            ->willReturn('serializedData');
        $this->serializerMock = $this->createMock(\Magento\Framework\Serialize\SerializerInterface::class);

        $this->serializerMock->expects($this->once())
            ->method('unserialize')
            ->with('serializedData')
            ->willReturn(
                [
                    'default' => [
                        'gateway' => "https://maps.googleapis.com/maps/api/geocode/json",
                        'country' => "#country",
                        'zip' => "#zip",
                        'region' => "#region",
                        'city' => "#city",
                        'street_1' => "#street_1",
                        'street_2' => "#street_2"
                    ],
                    'US' => [
                        'gateway' => "https://maps.googleapis.com/maps/api/geocode/json",
                        'country' => "#country",
                        'zip' => "#zip",
                        'region' => "#region",
                        'city' => "#city",
                        'street_1' => "#street_1",
                        'street_2' => "#street_2"
                    ],
                    'JP' => [
                        'gateway' => "https://maps.googleapis.com/maps/api/geocode/json",
                        'country' => "#country",
                        'zip' => "#zip",
                        'region' => "#region",
                        'city' => "#city",
                        'street_1' => "#street_1",
                        'street_2' => "#street_2"
                    ],
                ]);

        $this->converterConfig = $this->objectManager->getObject(
            \Magento\ZipCodeConverter\Model\Config\ConverterConfig::class,
            [
                'reader' => $this->reader,
                'cache' => $this->cache,
                'cacheId' => 'zip_code_converter_config',
                'serializer' => $this->serializerMock,
            ]
        );
    }

    public function testGetConfiguration()
    {
        $result = $this->converterConfig->getConfiguration('JP');
        $this->assertInternalType(
            'array',
            $result
        );
    }

    public function testGetDefaultConfiguration()
    {
        $result = $this->converterConfig->getDefaultConfiguration();
        $this->assertInternalType(
            'array',
            $result
        );
    }
}