<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Elasticsearch\Model\Adapter\Index\Config;

/**
 * Tokenizer configuration
 *
 * Magento expects that configuration is based on XML declaration at etc/esconfig.xml.
 * Custom implementation may ignore XML configuration but this is not recommended as may confuse user.
 *
 * Example of tokenizer XML configuration:
 *
 * <config>
 *  <tokenizer>
 *    <default><!-- default is required -->
 *       <type>standard</type><!-- type is required for all items -->
 *    </default>
 *    <jp_JP><!-- optional config for Japanese locale -->
 *       <type>kuromoji_tokenizer</type>
 *       <mode>extended</mode><!-- optional parameter specific for tokenizer defined in type element --!>
 *       <discard_punctuation>false</discard_punctuation>
 *       <user_dictionary>userdict_ja.txt</user_dictionary>
 *    <jp_JP>
 *  </tokenizer>
 * </config>
 *
 * @api
 */
interface EsTokenizerConfigInterface
{
    /**
     * Returns associative array with tokenizer configuration.
     * Required keys:
     *  - default - configuration of default tokenizer
     * Optional keys:
     *  - any valid locale code (e.g. en_Us) -  configuration of tokenizer that should be used for the locale
     *
     * @return array
     */
    public function getTokenizerInfo(): array;
}
