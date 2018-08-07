<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Elasticsearch\Model\Adapter\Index\Config;

use Magento\Framework\Config\ConverterInterface;

class Converter implements ConverterInterface
{
    /**
     * {@inheritdoc}
     */
    public function convert($source)
    {
        $stemmerInfo = $this->convertStemmer(
            $source->getElementsByTagName('stemmer')
        );

        $stopwordsInfo = $this->convertStopwords(
            $source->getElementsByTagName('stopwords_file')
        );

        $tokenizerInfo = $this->convertTokenizer(
            $source->getElementsByTagName('tokenizer')
        );

        $tokenFiltersInfo = $this->convertFilters(
            $source->getElementsByTagName('token_filters')
        );

        $charFiltersInfo = $this->convertFilters(
            $source->getElementsByTagName('char_filters')
        );

        return [
            'stemmerInfo' => $stemmerInfo,
            'stopwordsInfo' => $stopwordsInfo,
            'tokenizerInfo' => $tokenizerInfo,
            'tokenFiltersInfo' => $tokenFiltersInfo,
            'charFiltersInfo' => $charFiltersInfo,
        ];
    }

    /**
     * Convert XML config with stemmer
     *
     * @param \DOMNodeList $stemmer
     * @return array
     */
    private function convertStemmer(\DOMNodeList $stemmer): array
    {
        $stemmerInfo = [];
        foreach ($this->getItemNodes($stemmer) as $element) {
            $stemmerInfo[$element->localName]= $element->textContent;
        }
        return $stemmerInfo;
    }

    /**
     * Convert XML config with stop words
     *
     * @param \DOMNodeList $stopwords
     * @return array
     */
    private function convertStopwords(\DOMNodeList $stopwords): array
    {
        $stopwordsInfo = [];
        foreach ($this->getItemNodes($stopwords) as $node) {
            $stopwordsInfo[$node->localName]= $node->textContent;
        }
        return $stopwordsInfo;
    }

    /**
     * Convert XML config with tokenizer
     *
     * @param \DOMNodeList $tokenizer
     * @return array
     */
    private function convertTokenizer(\DOMNodeList $tokenizer): array
    {
        $tokenizerInfo = [];
        foreach ($this->getItemNodes($tokenizer) as $node) {
           $tokenizerInfo[$node->localName]= $this->convertConfigPart($node);
        }
        return $tokenizerInfo;
    }

    /**
     * Convert XML config with filters
     *
     * @param \DOMNodeList $filters
     * @return array
     */
    private function convertFilters(\DOMNodeList $filters): array
    {
        $filtersInfo = [];
        foreach ($this->getItemNodes($filters) as $node) {
            $filtersInfo[$node->localName] = $this->convertConfigPart($node);
        }
        return $filtersInfo;
    }

    /**
     * Fetch element nodes from config structure
     *
     * @param \DOMNodeList $nodeList
     * @return array
     */
    private function getItemNodes(\DOMNodeList $nodeList): array
    {
        $elements = [];
        foreach ($nodeList as $node) {
            /** @var \DOMNode $childNode */
            foreach ($this->elementChildNodes($node) as $childNode) {
                $elements[$childNode->localName] = $childNode;
            }
        }
        return $elements;
    }

    /**
     * Convert XML configuration holding ElasticSearch parameters to PHP data
     *
     * @param \DOMNode $node
     * @return mixed
     */
    private function convertConfigPart(\DOMNode $node) {
        $type = $this->detectConfigType($node);
        switch ($type) {
            case 'string':
                return $this->convertStringNode($node);
            case 'number':
                return $this->convertNumberNode($node);
            case 'boolean':
                return $this->convertBooleanNode($node);
            case 'list':
                return $this->convertListNode($node);
            case 'map':
                return $this->convertMapNode($node);
            case 'empty':
                return null;
            default:
                throw new \InvalidArgumentException('Invalid configuration provided. Unknown node type.');
        }
    }

    /**
     * Detect data type of node. Node may declare own type by xsi:type attribute.
     * If not declared, type will be detected automatically
     *
     * @param \DOMNode $node
     * @return string
     */
    private function detectConfigType(\DOMNode $node): string
    {
        $declaredType = $node->getAttribute('xsi:type');
        if (in_array($declaredType, ['string', 'number', 'boolean', 'list', 'map'])) {
            return $declaredType;
        }

        $childElementsCount = 0;
        foreach ($this->elementChildNodes($node) as $elementChildNode) {
            if ($elementChildNode->localName !== 'item') {
                return 'map';
            }
            $childElementsCount++;
        }

        if ($childElementsCount === 0) {
            return empty($node->nodeValue) ? 'empty' : 'string';
        }

        return 'list';
    }

    /**
     * Convert content of node which correspond to string ElasticSearch config parameter
     *
     * @param \DOMNode $node
     * @return string
     */
    private function convertStringNode(\DOMNode $node): string
    {
        $value = trim($node->textContent);
        return $value;
    }

    /**
     * Convert content of node which correspond to numeric ElasticSearch config parameter
     *
     * @param \DOMNode $node
     * @return int|float
     */
    private function convertNumberNode(\DOMNode $node)
    {
        $rawValue = trim($node->textContent);
        $value = filter_var($rawValue, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
        if ($value !== null) {
            return $value;
        }
        $value = filter_var($rawValue, FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE);
        if ($value !== null) {
            return $value;
        }
        throw new \InvalidArgumentException('Invalid configuration provided. Numeric value expected.');
    }

    /**
     * Convert content of node which correspond to boolean ElasticSearch config parameter
     *
     * @param \DOMNode $node
     * @return bool
     */
    private function convertBooleanNode(\DOMNode $node): bool
    {
        $rawValue = trim($node->textContent);
        $value = filter_var($rawValue, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        if ($value !== null) {
            return $value;
        }
        throw new \InvalidArgumentException('Invalid configuration provided. Boolean value expected.');
    }

    /**
     * Convert content of node which correspond to indexed array ElasticSearch config parameter
     *
     * @param \DOMNode $node
     * @return array
     */
    private function convertListNode(\DOMNode $node): array
    {
        $list = [];
        foreach ($this->elementChildNodes($node) as $child) {
            if ($child->localName !== 'item') {
                throw new \InvalidArgumentException(
                    'Invalid configuration provided. Only "item" elements expected in a list.'
                );
            }
            $list[] = $this->convertConfigPart($child);
        }
        return $list;
    }

    /**
     * Convert content of node which correspond to key-value ElasticSearch config parameter
     *
     * @param \DOMNode $node
     * @return array
     */
    private function convertMapNode(\DOMNode $node): array
    {
        $map = [];
        foreach ($this->elementChildNodes($node) as $child) {
            $map[$child->localName] = $this->convertConfigPart($child);
        }
        return $map;
    }

    /**
     * Generator to iterate only over child nodes of type element
     *
     * All elements marked with attribute disabled="true" will be skipped
     *
     * @param \DOMNode $node
     * @return \Generator
     */
    private function elementChildNodes(\DOMNode $node)
    {
        foreach ($node->childNodes as $child) {
            if (
                $child->nodeType === XML_ELEMENT_NODE
                && strcasecmp($child->getAttribute('disabled'), 'true') !== 0
            ) {
                yield $child;
            }
        }
    }
}
