<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
use Magento\Framework\Component\ComponentRegistrar;

$registrar = new ComponentRegistrar();
$registeredPath = $registrar->getPath(
    ComponentRegistrar::MODULE,
    'Magento_TestModuleElasticSearchConfig'
);
if ($registeredPath === null) {
    ComponentRegistrar::register(
        ComponentRegistrar::MODULE,
        'Magento_TestModuleElasticSearchConfig',
        __DIR__
    );
}
