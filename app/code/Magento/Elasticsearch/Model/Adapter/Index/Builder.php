<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Elasticsearch\Model\Adapter\Index;

use Magento\Framework\Locale\Resolver as LocaleResolver;
use Magento\Elasticsearch\Model\Adapter\Index\Config\EsConfigInterface;

class Builder implements BuilderInterface
{
    /**
     * @var LocaleResolver
     */
    protected $localeResolver;

    /**
     * @var EsConfigInterface
     */
    protected $esConfig;

    /**
     * Current store ID.
     *
     * @var int
     */
    protected $storeId;

    /**
     * @param LocaleResolver $localeResolver
     * @param EsConfigInterface $esConfig
     */
    public function __construct(
        LocaleResolver $localeResolver,
        EsConfigInterface $esConfig
    ) {
        $this->localeResolver = $localeResolver;
        $this->esConfig = $esConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function build()
    {
        $tokenizer = $this->getTokenizer();
        $analyzerFilter = $this->getCharFilterConfig();
        $filter = $this->getFilter();
        $charFilter = $this->getCharFilter();

        $settings = [
            'analysis' => [
                'analyzer' => [
                    'default' => [
                        'type' => 'custom',
                        'tokenizer' => key($tokenizer),
                        'filter' => $analyzerFilter,
                        'char_filter' => array_keys($charFilter)
                    ]
                ],
                'tokenizer' => $tokenizer,
                'filter' => $filter,
                'char_filter' => $charFilter,
            ],
        ];
        return $settings;
    }

    /**
     * {@inheritdoc}
     */
    public function setStoreId($storeId)
    {
        $this->storeId = $storeId;
    }

    /**
     * @return array
     */
    protected function getTokenizer()
    {
        $tokenizer = [
            'default_tokenizer' => $this->getTokenizerConfig(),
        ];
        return $tokenizer;
    }

    /**
     * @return array
     */
    protected function getFilter()
    {
        $filter = [
            'default_stemmer' => $this->getStemmerConfig(),
            'unique_stem' => [
                'type' => 'unique',
                'only_on_same_position' => true
            ]
        ];
        return $filter;
    }

    /**
     * @return array
     */
    protected function getCharFilter()
    {
        $charFilter = [
            'default_char_filter' => [
                'type' => 'html_strip'
            ],
        ];
        return $charFilter;
    }

    /**
     * @return array
     */
    protected function getStemmerConfig()
    {
        $stemmerInfo = $this->esConfig->getStemmerInfo();
        $this->localeResolver->emulate($this->storeId);
        $locale = $this->localeResolver->getLocale();
        if (isset($stemmerInfo[$locale])) {
            return [
                'type' => $stemmerInfo['type'],
                'language' => $stemmerInfo[$locale],
            ];
        }
        return [
            'type' => $stemmerInfo['type'],
            'language' => $stemmerInfo['default'],
        ];
    }

    protected function getTokenizerConfig()
    {
        $tokenizerInfo = $this->esConfig->getTokenizerInfo();
        $this->localeResolver->emulate($this->storeId);
        $locale = $this->localeResolver->getLocale();
        if (isset($tokenizerInfo[$locale])) {
            return [
                'type' => $tokenizerInfo[$locale]
            ];
        }
        return [
            'type' => $tokenizerInfo['default']
        ];
    }

    protected function getCharFilterConfig()
    {
        $charFilterInfo = $this->esConfig->getCharFilterInfo();
        $this->localeResolver->emulate($this->storeId);
        $locale = $this->localeResolver->getLocale();
        if (isset($charFilterInfo[$locale])) {
            return $charFilterInfo[$locale];
        }
        return $charFilterInfo['default'];
    }
}
