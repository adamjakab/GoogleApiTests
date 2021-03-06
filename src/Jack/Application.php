<?php
/**
 * Created by Adam Jakab.
 * Date: 06/05/15
 * Time: 12.14
 */

namespace Jack;
use Symfony\Component\Console\Application as BaseApplication;

class Application extends BaseApplication
{
	const NAME = 'Google API Tests - Console Application';
	const VERSION = '1.0';

	public function __construct()
	{
		parent::__construct(static::NAME, static::VERSION);
		$this->addCommands([
            new Console\Command\TestCommand()
        ]);
	}
}