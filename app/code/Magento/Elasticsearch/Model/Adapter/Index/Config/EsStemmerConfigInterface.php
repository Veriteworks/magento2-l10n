<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Elasticsearch\Model\Adapter\Index\Config;

/**
 * Stemming configuration interface
 *
 * @api
 */
interface EsStemmerConfigInterface
{
    /**
     * Returns associative array with stemming token filter configuration.
     * Required keys:
     *  - type (assumed value "stemmer")
     *  - default - a name of stemmer that should be used through unified interface by default
     * Optional keys:
     *  - any valid locale code (e.g. en_Us) -  a name of stemmer that should be used through unified interface
     *    for particular locale
     *
     * Stemming is used to convert tokens to normalized word stems which increase quality of search.
     * (E.g. In english token "conditional" after stemming will be converted to "condition")
     * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/analysis-stemmer-tokenfilter.html
     *
     * Note: New interface defined without return type declaration to be compatible with old EsConfigInterface
     * @return array
     */
    public function getStemmerInfo();
}
