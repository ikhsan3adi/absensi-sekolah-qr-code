<?php
/**
 * @created      28.08.2018
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2018 smiley
 * @license      MIT
 */

namespace chillerlan\SettingsExamples;

use chillerlan\Settings\SettingsContainerAbstract;

require_once __DIR__.'/../vendor/autoload.php';

class MyContainer extends SettingsContainerAbstract{
	protected $foo;
	protected $bar;
}

/** @var \chillerlan\Settings\SettingsContainerInterface $container */
$container = new MyContainer(['foo' => 'what']);
$container->bar = 'foo';

var_dump($container->toJSON()); // -> {"foo":"what","bar":"foo"}

// non-existing properties will be ignored:
$container->nope = 'what';

var_dump($container->nope); // -> NULL
