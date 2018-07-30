<?php
namespace Magento\Elasticsearch\Model\Adapter\Index\Config;

interface AdditionalEsConfigInterface
{
    /**
     * @return array
     */
    public function getTokenizerInfo();

    /**
     * @return array
     */
    public function getCharFilterInfo();
}
