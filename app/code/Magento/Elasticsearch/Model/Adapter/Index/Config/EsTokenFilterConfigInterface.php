<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Elasticsearch\Model\Adapter\Index\Config;

/**
 * Return Token filter configuration
 *
 * Magento expects that configuration is based on XML declaration at etc/esconfig.xml.
 * Custom implementation may ignore XML configuration but this is not recommended as may confuse user.
 *
 * Example of token filters XML configuration:
 * <token_filters>
 *   <default><!--required default configuration-->
 *     <lowercase/><!--standard filter-->
 *     <keyword_repeat/>
 *   </default>
 *   <en_US><!--optional configuration for a locale-->
 *      <lowercase/>
 *      <english_stop><!--customized filter-->
 *       <type>stop/type>
 *       <stopwords>_english_</stopwords>
 *     </english_stop>
 *     <standard_filter>lowercase</standard_filter>
 *   </en_US>
 * </token_filters>
 *
 * @api
 */
interface EsTokenFilterConfigInterface
{
    /**
     * Returns associative array with token filters.
     *
     * Required keys:
     *  - default - configuration of tokens used by default
     * Optional keys:
     *  - any valid locale code (e.g. en_Us) - configuration of filters used for the locale
     *
     * @return array
     */
    public function getTokenFiltersInfo(): array;

    /**
     * Returns list of filter names that should be used.
     * Always contains all filters returned by getTokenFilterInfo plus names of standard filters
     *
     * @return array
     */
    public function getTokenFiltersList(): array;
}