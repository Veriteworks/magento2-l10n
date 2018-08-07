<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Elasticsearch\Model\Adapter\Index\Config;

/**
 * Return Char filter configuration
 *
 * Magento expects that configuration is based on XML declaration at etc/esconfig.xml.
 * Custom implementation may ignore XML configuration but this is not recommended as may confuse user.
 *
 * Example of token filters XML configuration:
 * <char_filters>
 *     <default>
 *          <default_char_filter><!--customized filter>
 *              <type>html_strip</type>
 *          </default_char_filter>
 *     </default>
 *     <en_US>
 *          <my_char_filter>
 *               <type>html_strip</type>
 *               <escaped_tags>
 *                    <item>b</item>
 *               </escaped_tags>
 *          </my_char_filter>
 *     </en_US>
 * </char_filters>
 *
 * @api
 */
interface EsCharFilterConfigInterface
{
    /**
     * Returns associative array with token filters.
     *
     * Required keys:
     *  - default - configuration of tokens used by default
     * Optional keys:
     *  - any valid locale code (e.g. en_Us) - configuration of char filters used for the locale
     *
     * @return array
     */
    public function getCharFiltersInfo(): array;

    /**
     * Returns list of token names that should be used.
     * Always contains all filters returned by getCharFiltersInfo plus names of standard filters
     *
     * @return array
     */
    public function getCharFiltersList(): array;
}