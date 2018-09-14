<?php
namespace Magento\ZipCodeConverter\Model\Config;

use Magento\Framework\Config\CacheInterface;
use Magento\Framework\Config\Data;
use Magento\Framework\Config\ReaderInterface;
use Magento\Framework\Serialize\SerializerInterface;

class ConverterConfig extends Data implements ConverterConfigInterface
{
    /**
     * ConverterConfig constructor.
     * @param ReaderInterface $reader
     * @param CacheInterface $cache
     * @param string $cacheId
     * @param SerializerInterface|null $serializer
     */
    public function __construct(ReaderInterface $reader,
                                CacheInterface $cache,
                                string $cacheId,
                                SerializerInterface $serializer = null)
    {
        parent::__construct($reader, $cache, $cacheId, $serializer);
    }

    /**
     * @param $iso2CountryCode
     * @return array|mixed|null
     */
    public function getConfiguration($iso2CountryCode)
    {
        return $this->get($iso2CountryCode);
    }

    /**
     * @return array|mixed|null
     */
    public function getDefaultConfiguration()
    {
        return $this->get('default');
    }
}