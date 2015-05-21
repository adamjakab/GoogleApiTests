<?php
namespace Jack\Console\Command;

use Symfony\Component\Console\Command\Command as ConsoleCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Command extends ConsoleCommand
{
	/** @var  Array */
	protected $config;

	/** @var  InputInterface */
	protected $cmdInput;

	/** @var  OutputInterface */
	protected $cmdOutput;

	/**
	 * @param string $name
	 */
	public function __construct($name = null) {
		parent::__construct($name);
	}

	protected function _execute(InputInterface $input, OutputInterface $output) {
		$this->cmdInput = $input;
		$this->cmdOutput = $output;
	}

	/**
	 * @param string $msg
	 */
	protected function log($msg) {
		$this->cmdOutput->writeln($msg);
	}
}