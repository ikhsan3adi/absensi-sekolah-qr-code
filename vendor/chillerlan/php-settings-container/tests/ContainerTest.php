<?php
/**
 * Class ContainerTraitTest
 *
 * @created      28.08.2018
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2018 Smiley
 * @license      MIT
 */

namespace chillerlan\SettingsTest;

use PHPUnit\Framework\TestCase;
use JsonException, TypeError;
use function sha1;

class ContainerTest extends TestCase{

	public function testConstruct(){
		$container = new TestContainer([
			'test1' => 'test1',
			'test2' => true,
			'test3' => 'test3',
			'test4' => 'test4',
		]);

		$this::assertSame('test1', $container->test1);
		$this::assertSame(true, $container->test2);
		$this::assertNull($container->test3);
		$this::assertSame('test4', $container->test4);

		$this::assertSame('success', $container->testConstruct);
	}

	public function testGet(){
		$container = new TestContainer;

		$this::assertSame('foo', $container->test1);
		$this::assertNull($container->test2);
		$this::assertNull($container->test3);
		$this::assertNull($container->test4);
		$this::assertNull($container->foo);

		// isset test
		$this::assertTrue(isset($container->test1));
		$this::assertFalse(isset($container->test2));
		$this::assertFalse(isset($container->test3));
		$this::assertFalse(isset($container->test4));
		$this::assertFalse(isset($container->foo));

		// custom getter
		$container->test6 = 'foo';
		$this::assertSame(sha1('foo'), $container->test6);
		// nullable/isset test
		$container->test6 = null;
		$this::assertFalse(isset($container->test6));
		$this::assertSame('null', $container->test6);
	}

	public function testSet(){
		$container = new TestContainer;
		$container->test1 = 'bar';
		$container->test2 = false;
		$container->test3 = 'nope';

		$this::assertSame('bar', $container->test1);
		$this::assertSame(false, $container->test2);
		$this::assertNull($container->test3);

		// unset
		unset($container->test1);
		$this::assertFalse(isset($container->test1));

		// custom setter
		$container->test5 = 'bar';
		$this::assertSame('bar_test5', $container->test5);
	}

	public function testToArray(){
		$container = new TestContainer([
			'test1'         => 'no',
			'test2'         => true,
			'testConstruct' => 'success',
		]);

		$this::assertSame([
			'test1'         => 'no',
			'test2'         => true,
			'testConstruct' => 'success',
			'test4'         => null,
			'test5'         => null,
			'test6'         => null
		], $container->toArray());
	}

	public function testToJSON(){
		$container = (new TestContainer)->fromJSON('{"test1":"no","test2":true,"testConstruct":"success"}');

		$expected  = '{"test1":"no","test2":true,"testConstruct":"success","test4":null,"test5":null,"test6":null}';

		$this::assertSame($expected, $container->toJSON());
		$this::assertSame($expected, (string)$container);
	}

	public function testFromJsonException(){
		$this->expectException(JsonException::class);
		(new TestContainer)->fromJSON('-');

	}
	public function testFromJsonTypeError(){
		$this->expectException(TypeError::class);
		(new TestContainer)->fromJSON('2');
	}

}
