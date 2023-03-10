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

// from library #1
trait SomeOptions{
	protected string $foo = '';

	// this method will be called in SettingsContainerAbstract::__construct() after the properties have been set
	protected function SomeOptions(){
		// just some constructor stuff...
		$this->foo = strtoupper($this->foo);
	}
}

// from library #2
trait MoreOptions{
	protected string $bar = 'whatever'; // provide default values
}

$commonOptions = [
	// SomeOptions
	'foo' => 'whatever',
	// MoreOptions
	'bar' => 'nothing',
];

// now plug the several library options together to a single object

/**
 * @property string $foo
 * @property string $bar
 */
class MySettings extends SettingsContainerAbstract{
	use SomeOptions, MoreOptions; // ...
};

$container = new MySettings($commonOptions);

var_dump($container->foo); // -> WHATEVER (constructor ran strtoupper on the value)
var_dump($container->bar); // -> nothing
