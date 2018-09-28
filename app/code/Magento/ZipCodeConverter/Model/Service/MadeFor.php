<?php
namespace Magento\ZipCodeConverter\Model\Service;

class MadeFor implements ConverterInterface
{
    const CODE = 'madefor';

    private $resolver;

    public function __construct(
        \Magento\Framework\Locale\ResolverInterface $resolver
    )
    {
        $this->resolver = $resolver;
    }


    public function getCredential()
    {
        return [
            'credentials' => ['key' => '', 'lang' => $this->getCurrentLocale()]
        ];
    }

    /**
     * return current locale code
     */
    private function getCurrentLocale()
    {
        if($this->resolver->getLocale() == 'ja_JP')
        {
            return 'ja';
        }

        return 'en';
    }
}