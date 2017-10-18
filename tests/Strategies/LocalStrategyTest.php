<?php
/**
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Authentication\Tests\Strategies;

use Joomla\Authentication\Authentication;
use Joomla\Authentication\Password\HandlerInterface;
use Joomla\Authentication\Strategies\LocalStrategy;
use Joomla\Input\Input;
use PHPUnit\Framework\TestCase;

/**
 * Test class for Joomla\Authentication\Strategies\LocalStrategy
 */
class LocalStrategyTest extends TestCase
{
	/**
	 * @var  Input|\PHPUnit_Framework_MockObject_MockObject
	 */
	private $input;

	/**
	 * @var  HandlerInterface|\PHPUnit_Framework_MockObject_MockObject
	 */
	private $passwordHandler;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 */
	protected function setUp()
	{
		$this->input           = $this->getMockBuilder('Joomla\\Input\\Input')->getMock();
		$this->passwordHandler = $this->getMockBuilder('Joomla\\Authentication\\Password\\HandlerInterface')->getMock();
	}

	/**
	 * Tests the authenticate method with valid credentials.
	 */
	public function testValidPassword()
	{
		$this->input->expects($this->any())
			->method('get')
			->willReturnArgument(0);

		$this->passwordHandler->expects($this->any())
			->method('validatePassword')
			->willReturn(true);

		$credentialStore = [
			'username' => '$2y$10$.vpEGa99w.WUetDFJXjMn.RiKRhZ/ImzxtOjtoJ0VFDV8S7ua0uJG'
		];

		$localStrategy = new LocalStrategy($this->input, $credentialStore, $this->passwordHandler);

		$this->assertEquals('username', $localStrategy->authenticate());

		$this->assertEquals(Authentication::SUCCESS, $localStrategy->getResult());
	}

	/**
	 * Tests the authenticate method with invalid credentials.
	 */
	public function testInvalidPassword()
	{
		$this->input->expects($this->any())
			->method('get')
			->willReturnArgument(0);

		$this->passwordHandler->expects($this->any())
			->method('validatePassword')
			->willReturn(false);

		$credentialStore = [
			'username' => '$2y$10$.vpEGa99w.WUetDFJXjMn.RiKRhZ/ImzxtOjtoJ0VFDV8S7ua0uJH'
		];

		$localStrategy = new LocalStrategy($this->input, $credentialStore, $this->passwordHandler);

		$this->assertEquals(false, $localStrategy->authenticate());

		$this->assertEquals(Authentication::INVALID_CREDENTIALS, $localStrategy->getResult());
	}

	/**
	 * Tests the authenticate method with no credentials provided.
	 */
	public function testNoPassword()
	{
		$this->input->expects($this->any())
			->method('get')
			->willReturn(false);

		$this->passwordHandler->expects($this->never())
			->method('validatePassword');

		$credentialStore = [
			'username' => '$2y$10$.vpEGa99w.WUetDFJXjMn.RiKRhZ/ImzxtOjtoJ0VFDV8S7ua0uJH'
		];

		$localStrategy = new LocalStrategy($this->input, $credentialStore, $this->passwordHandler);

		$this->assertEquals(false, $localStrategy->authenticate());

		$this->assertEquals(Authentication::NO_CREDENTIALS, $localStrategy->getResult());
	}

	/**
	 * Tests the authenticate method with credentials for an unknown user.
	 */
	public function testUserNotExist()
	{
		$this->input->expects($this->any())
			->method('get')
			->willReturnArgument(0);

		$this->passwordHandler->expects($this->never())
			->method('validatePassword');

		$credentialStore = [
			'jimbob' => '$2y$10$.vpEGa99w.WUetDFJXjMn.RiKRhZ/ImzxtOjtoJ0VFDV8S7ua0uJH'
		];

		$localStrategy = new LocalStrategy($this->input, $credentialStore, $this->passwordHandler);

		$this->assertEquals(false, $localStrategy->authenticate());

		$this->assertEquals(Authentication::NO_SUCH_USER, $localStrategy->getResult());
	}
}
