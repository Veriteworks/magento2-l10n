<?php
namespace Magento\ZipCodeConverter\Block;

use Magento\Framework\View\Element\Template;

class Widget extends Template
{

    protected $_template = 'Magento_ZipCodeConverter::widget.phtml';

    /**
     * Widget constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

}