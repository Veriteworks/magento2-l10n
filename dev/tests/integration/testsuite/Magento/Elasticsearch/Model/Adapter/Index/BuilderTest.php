<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Elasticsearch\Model\Adapter\Index;

use Magento\Framework\Module\ModuleList;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppIsolation enabled
 * @magentoCache config disabled
 */
class BuilderTest extends TestCase
{
    public function testDefaultConfig()
    {
        $builder = $this->getBuilder();
        $config = $builder->build();
        $this->assertConfigHasCorrectStructure($config);
        $this->assertTokenizerConfigured($config);
        $this->assertCustomAnalyzerConfigured($config);
    }

    /**
     * Apply italian locale to verify merged configuration from test modules
     * @magentoConfigFixture default_store general/locale/code it_IT
     */
    public function testMergedConfig()
    {
        $builder = $this->getBuilder();
        $config = $builder->build();

        $this->assertOverrideElement($config);
        $this->assertOverrideListElement($config);
        $this->assertAddListElement($config);
        $this->assertDisabling($config);
    }

    private function getBuilder(): BuilderInterface
    {
        return Bootstrap::getObjectManager()->get(BuilderInterface::class);
    }

    private function assertConfigHasCorrectStructure($config)
    {
        $this->assertArrayHasKey('analysis', $config, 'Analysis configuration expected.');

        $analysis = $config['analysis'];
        $this->assertArrayHasKey('analyzer', $analysis, 'Analyzer configuration expected.');
        $this->assertArrayHasKey('tokenizer', $analysis, 'Tokenizer configuration expected.');
        $this->assertArrayHasKey('filter', $analysis, 'Token filters configuration expected.');
        $this->assertArrayHasKey('char_filter', $analysis, 'Char filters configuration expected.');
    }

    private function assertTokenizerConfigured($config)
    {
        $this->assertTrue(
            is_array($config['analysis']['tokenizer']),
            'Tokenizer configuration should be assoc array.'
        );
        $this->assertEquals(
            1,
            count($config['analysis']['tokenizer']),
            'Only single tokenizer is expected.'
        );
        $tokenizer = current($config['analysis']['tokenizer']);
        $this->assertTrue(
            isset($tokenizer['type']),
            'Tokenizer type is required.'
        );
    }

    private function assertCustomAnalyzerConfigured($config)
    {
        $this->assertTrue(
            isset($config['analysis']['analyzer']['default']),
            'Default analyzer configuration expected.'
        );
        $this->assertTrue(
            count($config['analysis']['analyzer']) === 1,
            'Single analyzer declaration expected.'
        );
        $this->assertTrue(
            isset($config['analysis']['analyzer']['default']['type']),
            'Analyzer type declaration expected.'
        );
        $this->assertEquals(
            'custom',
            $config['analysis']['analyzer']['default']['type'],
            'Analyzer type expected to be "custom".'
        );

        $this->assertTokenizerIsUsedByAnalyzer($config);
        $this->assertAllCutomFiltersAreUsed($config, 'filter');
        $this->assertAllCutomFiltersAreUsed($config, 'char_filter');
    }

    private function assertTokenizerIsUsedByAnalyzer($config)
    {
        $this->assertTrue(
            isset($config['analysis']['analyzer']['default']['tokenizer']),
            'Analyzer tokenizer configuration expected.'
        );
        $configuredTokenizer = $config['analysis']['analyzer']['default']['tokenizer'];
        $this->assertTrue(
            isset($config['analysis']['tokenizer'][$configuredTokenizer]),
            'Analyzer must use declared tokenizer.'
        );
    }

    private function assertAllCutomFiltersAreUsed($config, $filterType)
    {
        if (!isset($config['analysis']['analyzer']['default'][$filterType])) {
            // filter type is not used by analyzer, nothing to check
            return;
        }
        if (!isset($config['analysis'][$filterType])) {
            // no customized filters of the type, nothing to check
            return;
        }

        $usedFilters = $config['analysis']['analyzer']['default'][$filterType];
        $customFilters = array_keys($config['analysis'][$filterType]);
        $this->assertEmpty(
            array_diff($customFilters, $usedFilters),
            'All customized filters expected to be used.'
        );
    }

    private function assertOverrideElement($config)
    {
        $this->assertEquals(
            'light_italian',
            $config['analysis']['filter']['default_stemmer']['language'],
            'Value "light_italian" must override "italian".'
        );
    }

    private function assertOverrideListElement($config)
    {
        $italianElisionFilterArticles = $config['analysis']['filter']['italian_elision']['articles'];
        $this->assertContains(
            'c',
            $italianElisionFilterArticles,
            'List item must be overridden by ref.'
        );
        $this->assertNotContains(
            'should be overridden',
            $italianElisionFilterArticles,
            'List item must be overridden by ref.'
        );
    }

    private function assertAddListElement($config)
    {
        $italianElisionFilterArticles = $config['analysis']['filter']['italian_elision']['articles'];
        $this->assertContains(
            'd',
            $italianElisionFilterArticles,
            'List item must be added with ref not in use.'
        );
    }

    private function assertDisabling($config)
    {
        $this->assertNotContains(
            'other_char_filter',
            $config['analysis']['analyzer']['default']['char_filter'],
            'Disabled filter must not be used.'
        );
        $this->assertArrayNotHasKey(
            'other_char_filter',
            $config['analysis']['char_filter'],
            'Disabled filter must not be present.'
        );
    }
}
