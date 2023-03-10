<?php
/**
 * Class TestContainer
 *
 * @created      28.08.2018
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2018 Smiley
 * @license      MIT
 */

namespace chillerlan\SettingsTest;

use chillerlan\Settings\SettingsContainerAbstract;

/**
 * @property $test1
 * @property $test2
 * @property $test3
 * @property $test4
 * @property $test5
 * @property $test6
 */
class TestContainer extends SettingsContainerAbstract{
	use TestOptionsTrait;

	private string $test3 = 'what';
}
