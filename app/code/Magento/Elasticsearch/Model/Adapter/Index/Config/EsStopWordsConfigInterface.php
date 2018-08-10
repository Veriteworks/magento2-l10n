<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Elasticsearch\Model\Adapter\Index\Config;

/**
 * Stops words configuration interface
 *
 * @api
 */
interface EsStopWordsConfigInterface
{
    /**
     * Returns list of files with stop words where
     * keys are locale code (e.g. en_US) or keyword "default" and
     * values are paths to CSV files with defined stop words (one stop word per line).
     * List must contain at least one value with key "default"
     *
     *
     * Stop words list is used to filter tokens from tokens stream during analysis.
     * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/analysis-stop-tokenfilter.html
     *
     * Note: New interface defined without return type declaration to be compatible with old EsConfigInterface
     * @return array
     */
    public function getStopwordsInfo();
}
