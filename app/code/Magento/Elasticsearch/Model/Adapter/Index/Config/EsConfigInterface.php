<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Elasticsearch\Model\Adapter\Index\Config;

/**
 * @api
 * @since 100.1.0
 *
 * @deprecated Use corresponding interface which provide more specific data
 * @see \Magento\Elasticsearch\Model\Adapter\Index\Config\EsStemmerConfigInterface
 * @see \Magento\Elasticsearch\Model\Adapter\Index\Config\EsStopWordsConfigInterface
 */
interface EsConfigInterface extends EsStemmerConfigInterface, EsStopWordsConfigInterface
{
    /**
     * @return array
     * @since 100.1.0
     */
    public function getStemmerInfo();

    /**
     * @return array
     * @since 100.1.0
     */
    public function getStopwordsInfo();
}
