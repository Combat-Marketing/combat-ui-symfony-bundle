<?php

declare(strict_types=1);

/*
 * Combat UI Symfony Bundle
 *
 * This source file is subject to the MIT license bundled with this package in the file LICENSE.
 *
 * @copyright Copyright (c) 2026 Combat Jongerenmarketing en -communicatie B.V. (https://www.combat.nl)
 * @license MIT https://opensource.org/licenses/MIT
 * @author Combat UI contributors
 *
 */

if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
    throw new RuntimeException('Vendor autoload not found. Run "composer install" first.');
}

require __DIR__ . '/../vendor/autoload.php';

// KernelTestCase environment. PHPUnit sets these through the <php> section of phpunit.xml.dist;
// when the suite runs through Codeception this bootstrap has to provide them itself.
$_SERVER['KERNEL_CLASS'] ??= \CombatUI\Bundle\CoreBundle\Tests\Kernel\Kernel::class;
$_SERVER['APP_ENV'] ??= 'test';
$_SERVER['APP_DEBUG'] ??= '1';

$_ENV['KERNEL_CLASS'] ??= $_SERVER['KERNEL_CLASS'];
$_ENV['APP_ENV'] ??= $_SERVER['APP_ENV'];
$_ENV['APP_DEBUG'] ??= $_SERVER['APP_DEBUG'];
