<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Elasticsearch\Model\Adapter\Index;

use Magento\Elasticsearch\Model\Adapter\Index\Config\EsCharFilterConfigInterface;
use Magento\Elasticsearch\Model\Adapter\Index\Config\EsStemmerConfigInterface;
use Magento\Elasticsearch\Model\Adapter\Index\Config\EsTokenFilterConfigInterface;
use Magento\Elasticsearch\Model\Adapter\Index\Config\EsTokenizerConfigInterface;
use Magento\Framework\App\ObjectManager;
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
     * @deprecated
     */
    protected $esConfig;

    /**
     * Current store ID.
     *
     * @var int
     */
    protected $storeId;

    /**
     * @var EsStemmerConfigInterface
     */
    private $stemmerConfig;

    /**
     * @var EsTokenizerConfigInterface
     */
    private $tokenizerConfig;

    /**
     * @var EsTokenFilterConfigInterface
     */
    private $tokenFilterConfig;

    /**
     * @var EsCharFilterConfigInterface
     */
    private $charFilterConfig;

    /**
     * @var array
     */
    private $detectedLocales;

    /**
     * Builder constructor.
     * @param LocaleResolver $localeResolver
     * @param EsConfigInterface $esConfig @deprecated
     * @param EsStemmerConfigInterface $stemmerConfig
     * @param EsTokenizerConfigInterface $tokenizerConfig
     * @param EsTokenFilterConfigInterface $tokenFilterConfig
     * @param EsCharFilterConfigInterface $charFilterConfig
     */
    public function __construct(
        LocaleResolver $localeResolver,
        EsConfigInterface $esConfig,
        EsStemmerConfigInterface $stemmerConfig = null,
        EsTokenizerConfigInterface $tokenizerConfig = null,
        EsTokenFilterConfigInterface $tokenFilterConfig = null,
        EsCharFilterConfigInterface $charFilterConfig = null
    ) {
        $this->localeResolver = $localeResolver;
        $this->esConfig = $esConfig;

        $om = ObjectManager::getInstance();
        $this->stemmerConfig = $stemmerConfig ?: $om->get(EsStemmerConfigInterface::class);
        $this->tokenizerConfig = $tokenizerConfig ?: $om->get(EsTokenizerConfigInterface::class);
        $this->tokenFilterConfig = $tokenFilterConfig ?: $om->get(EsTokenFilterConfigInterface::class);
        $this->charFilterConfig = $charFilterConfig ?: $om->get(EsCharFilterConfigInterface::class);

        $this->detectedLocales = [];
    }

    /**
     * {@inheritdoc}
     */
    public function build()
    {
        $tokenizer = $this->getTokenizer();
        $customizedTokenFilters = $this->getFilter();
        $tokenFiltersInUse = $this->getFilterForAnalyzer();
        $customizedCharFilters = $this->getCharFilter();
        $charFiltersInUse = $this->getCharFilterForAnalyzer();

        $settings = [
            'analysis' => [
                'analyzer' => [
                    'default' => [
                        'type' => 'custom',
                        'tokenizer' => key($tokenizer),
                        'filter' => $tokenFiltersInUse,
                        'char_filter' => $charFiltersInUse,
                    ]
                ],
                'tokenizer' => $tokenizer,
                'filter' => $customizedTokenFilters,
                'char_filter' => $customizedCharFilters,
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
    protected function getTokenizer(): array
    {
        $tokenizer = [
            'default_tokenizer' => $this->selectActiveLocaleConfig(
                $this->tokenizerConfig->getTokenizerInfo(),
                [
                    'type' => 'standard'
                ]
            )
        ];
        return $tokenizer;
    }

    /**
     * Get customized filters config
     *
     * @return array
     */
    protected function getFilter()
    {
        $filters = $this->getPredefinedCustomizedTokenFilters();

        $filters = array_merge(
            $filters,
            $this->selectActiveLocaleConfig(
                $this->tokenFilterConfig->getTokenFiltersInfo(),
                []
            )
        );

        return $filters;
    }

    /**
     * Return list of token filter names to use in analyzer
     *
     * @return array
     */
    private function getFilterForAnalyzer(): array
    {
        $predefinedStandardFilters = ['lowercase', 'keyword_repeat'];

        $filters = array_merge(
            $predefinedStandardFilters,
            array_keys($this->getPredefinedCustomizedTokenFilters()),
            $this->selectActiveLocaleConfig(
                $this->tokenFilterConfig->getTokenFiltersList(),
                []
            )
        );

        $filters = array_values(array_unique($filters));

        return $filters;
    }

    /**
     * Get configuration of expected token filters.
     * Configuration of predefined filters may be changed from config.
     *
     * @return array
     */
    private function getPredefinedCustomizedTokenFilters(): array
    {
        return [
            'default_stemmer' => $this->getStemmerConfig(),
            'unique_stem' => [
                'type' => 'unique',
                'only_on_same_position' => true
            ]
        ];
    }

    /**
     * Get customized filters config
     *
     * @return array
     */
    protected function getCharFilter(): array
    {
        $filters = $this->getPredefinedCustomizedCharFilters();

        $filters = array_merge(
            $filters,
            $this->selectActiveLocaleConfig(
                $this->charFilterConfig->getCharFiltersInfo(),
                []
            )
        );

        return $filters;
    }

    /**
     * Return list of char filter names to use in analyzer
     *
     * @return array
     */
    private function getCharFilterForAnalyzer(): array
    {
        $predefinedStandardFilters = [];

        $filters = array_merge(
            $predefinedStandardFilters,
            array_keys($this->getPredefinedCustomizedCharFilters()),
            $this->selectActiveLocaleConfig(
                $this->charFilterConfig->getCharFiltersList(),
                []
            )
        );

        $filters = array_values(array_unique($filters));

        return $filters;
    }

    /**
     * Get configuration of expected char filters.
     * Configuration of predefined filters may be changed from config.
     *
     * @return array
     */
    private function getPredefinedCustomizedCharFilters(): array
    {
        return [
            'default_char_filter' => [
                'type' => 'html_strip',
            ],
        ];
    }

    /**
     * @return array
     */
    protected function getStemmerConfig()
    {
        $stemmerConfig = $this->stemmerConfig->getStemmerInfo();
        return [
            'type' => $stemmerConfig['type'],
            'language' => $this->selectActiveLocaleConfig($stemmerConfig, 'english'),
        ];
    }

    /**
     * Read locale aware config option
     *
     * @param array $data
     * @param mixed $default
     * @return mixed
     */
    private function selectActiveLocaleConfig(array $data, $default)
    {
        $locale = $this->getLocale();
        if (isset($data[$locale])) {
            return  $data[$locale];
        }
        return array_key_exists('default', $data) ? $data['default'] : $default;
    }

    /**
     * Get locale code from LocaleResolver by store.
     * If no locale code provided fallback to "default"
     *
     * @return string
     */
    private function getLocale() : string
    {
        if (!isset($this->detectedLocales[$this->storeId])) {
            $this->localeResolver->emulate($this->storeId);
            $this->detectedLocales[$this->storeId] = $this->localeResolver->getLocale() ?: 'default';
            $this->localeResolver->revert();
        }
        return $this->detectedLocales[$this->storeId];
    }
}
