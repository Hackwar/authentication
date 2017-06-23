<?php
/**
 * @copyright  Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Authentication\Tests;

use Joomla\Authentication\Authentication;
use Joomla\Authentication\AuthenticationStrategyInterface;
use PHPUnit\Framework\TestCase;

/**
 * Test class for Authentication
 *
 * @since  1.0
 */
class AuthenticationTest extends TestCase
{
	/**
	 * Object being tested
	 *
	 * @var  Authentication
	 */
	private $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	protected function setUp()
	{
		$this->object = new Authentication;
	}

	/**
	 * Tests the authenticate method, specifying the strategy by name.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testSingleStrategy()
	{
		$mockStrategy = $this->getMockBuilder(AuthenticationStrategyInterface::class)->getMock();

		$this->object->addStrategy('mock', $mockStrategy);

		$mockStrategy->expects($this->once())
			->method('authenticate')
			->with()
			->will($this->returnValue(false));

		$this->assertFalse($this->object->authenticate(['mock']));
	}

	/**
	 * Tests the authenticate method, using all strategies
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testSingleStrategyEmptyArray()
	{
		$mockStrategy = $this->getMockBuilder(AuthenticationStrategyInterface::class)->getMock();

		$this->object->addStrategy('mock', $mockStrategy);

		$mockStrategy->expects($this->once())
			->method('authenticate')
			->with()
			->will($this->returnValue(false));

		$this->assertFalse($this->object->authenticate());
	}

	/**
	 * Tests the authenticate method, using some strategies.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testSomeStrategies()
	{
		$mockStrategy1 = $this->getMockBuilder(AuthenticationStrategyInterface::class)->getMock();
		$mockStrategy2 = $this->getMockBuilder(AuthenticationStrategyInterface::class)->getMock();
		$mockStrategy3 = $this->getMockBuilder(AuthenticationStrategyInterface::class)->getMock();

		$this->object->addStrategy('mock1', $mockStrategy1);
		$this->object->addStrategy('mock2', $mockStrategy2);
		$this->object->addStrategy('mock3', $mockStrategy3);

		$mockStrategy1->expects($this->never())
			->method('authenticate');

		$mockStrategy2->expects($this->once())
			->method('authenticate')
			->with()
			->will($this->returnValue('jimbob'));

		$mockStrategy3->expects($this->never())
			->method('authenticate');

		$this->assertEquals('jimbob', $this->object->authenticate(['mock2', 'mock3']));
	}

	/**
	 * Tests the authenticate method, using a non registered strategy
	 *
	 * @return  void
	 *
	 * @since   1.0
	 *
	 * @expectedException  RuntimeException
	 */
	public function testStrategiesException()
	{
		$this->object->authenticate(['mock1']);
	}

	/**
	 * Tests getting the result back.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testGetResults()
	{
		$mockStrategy = $this->getMockBuilder(AuthenticationStrategyInterface::class)->getMock();

		$this->object->addStrategy('mock', $mockStrategy);

		$mockStrategy->expects($this->once())
			->method('authenticate')
			->with()
			->will($this->returnValue(false));

		$mockStrategy->expects($this->once())
			->method('getResult')
			->with()
			->will($this->returnValue(Authentication::SUCCESS));

		$this->object->authenticate();

		$this->assertEquals(
			['mock' => Authentication::SUCCESS],
			$this->object->getResults()
		);
	}
}
