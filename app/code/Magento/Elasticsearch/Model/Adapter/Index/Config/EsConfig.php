<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Elasticsearch\Model\Adapter\Index\Config;

use Magento\Framework\Config\Data;
use Magento\Framework\Config\CacheInterface;
use Magento\Framework\Config\ReaderInterface;
use Magento\Framework\Serialize\SerializerInterface;

class EsConfig extends Data implements
    EsStemmerConfigInterface,
    EsStopWordsConfigInterface,
    EsTokenizerConfigInterface,
    EsTokenFilterConfigInterface,
    EsCharFilterConfigInterface,
    EsConfigInterface // obsolete, left for backward compatibility
{
    /**
     * @param ReaderInterface $reader
     * @param CacheInterface $cache
     * @param string $cacheId
     * @param SerializerInterface|null $serializer
     */
    public function __construct(
        ReaderInterface $reader,
        CacheInterface $cache,
        $cacheId,
        SerializerInterface $serializer = null
    ) {
        parent::__construct($reader, $cache, $cacheId, $serializer);
    }

    /**
     * {@inheritdoc}
     */
    public function getStemmerInfo()
    {
        return $this->get('stemmerInfo');
    }

    /**
     * {@inheritdoc}
     */
    public function getStopwordsInfo()
    {
        return $this->get('stopwordsInfo');
    }

    /**
     * @inheritDoc
     */
    public function getTokenizerInfo(): array
    {
        return $this->get('tokenizerInfo');
    }

    /**
     * @inheritDoc
     */
    public function getTokenFiltersInfo(): array
    {
        return $this->getFiltersInfo('tokenFiltersInfo');
    }

    /**
     * @inheritDoc
     */
    public function getTokenFiltersList(): array
    {
        return $this->getFiltersList('tokenFiltersInfo');
    }

    /**
     * @inheritDoc
     */
    public function getCharFiltersInfo(): array
    {
        return $this->getFiltersInfo('charFiltersInfo');
    }

    /**
     * @inheritDoc
     */
    public function getCharFiltersList(): array
    {
        return $this->getFiltersList('charFiltersInfo');
    }

    /**
     * Get customized filters info
     *
     * @param string $dataSource
     * @return array
     */
    private function getFiltersInfo(string $dataSource): array
    {
        $rawData = $this->get($dataSource);
        $data = array_map('array_filter', $rawData);
        return $data;
    }

    /**
     * Get used filters list, including standard
     *
     * @param string $dataSource
     * @return array
     */
    private function getFiltersList(string $dataSource): array
    {
        $rawData = $this->get($dataSource);
        $data = array_map('array_keys', $rawData);
        return $data;
    }
}
