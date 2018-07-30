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

        $tokenizerFilterInfo = $this->convertTokenizerFilter(
            $source->getElementsByTagName('tokenizer_filter')
        );

        return [
            'stemmerInfo' => $stemmerInfo,
            'stopwordsInfo' => $stopwordsInfo,
            'tokenizerInfo' => $tokenizerInfo,
            'charFilterInfo' => $tokenizerFilterInfo
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
           $tokenizerInfo[$node->localName]= trim($node->textContent);
        }
        return $tokenizerInfo;
    }

    /**
     * Convert XML config with tokenizer filter
     *
     * @param \DOMNodeList $tokenizerFilter
     * @return array
     */
    private function convertTokenizerFilter(\DOMNodeList $tokenizerFilter): array
    {
        $tokenizerFilterInfo = [];
        foreach ($this->getItemNodes($tokenizerFilter) as $node) {
            $tokenizerFilterInfo[$node->localName] = $this->convertItemNodeToArray($node);
        }
        return $tokenizerFilterInfo;
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
            foreach ($node->childNodes as $childNode) {
                if ($childNode->nodeType === XML_ELEMENT_NODE) {
                    $elements[$childNode->localName] = $childNode;
                }
            }
        }
        return $elements;
    }

    /**
     * Convert Item Node to Array
     *
     * @param $node
     * @return array
     */
    private function convertItemNodeToArray($node)
    {
        $nodeValue = [];
        foreach ($node->childNodes as $child) {
            $value = trim($child->textContent);
            if ($value) {
                $nodeValue[] = trim($child->textContent);
            }
        }

        return $nodeValue;
    }
}
