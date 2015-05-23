<?php
namespace Jack\Console\Command;

use Jack\Google\GoogleAuth;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestCommand extends Command
{
	/**
	 * @param string $name
	 */
	public function __construct($name = null) {
		parent::__construct($name);
	}

	/**
	 * Configure command
	 */
	protected function configure() {
		$this->setName("test")->setDescription("Run some tests")->setDefinition(
			[
				new InputArgument('config_file', InputArgument::REQUIRED, 'The yaml(.yml) configuration file'),
			]
		);
	}

	/**
	 * @param InputInterface  $input
	 * @param OutputInterface $output
	 * @return bool
	 */
	protected function execute(InputInterface $input, OutputInterface $output) {
		parent::_execute($input, $output);
		$this->checkConfiguration();
		$this->doit();
		$this->log("\nDone");
	}

	private function doit() {
        $googleAuth = new GoogleAuth($this->config);
        $at = json_decode($googleAuth->getAccessToken());
        $access_token = $at->access_token;
        //echo "\naccess token: " . $access_token;

        //$url = 'https://www.google.com/m8/feeds/contacts/default/full?alt=json&v=3.0&oauth_token='.$access_token;
        $url = 'https://www.google.com/m8/feeds/contacts/default/full?max-results=1&alt=json&v=3.0&oauth_token='.$access_token;
        $url = 'https://www.google.com/m8/feeds/contacts/default/full?max-results=1&alt=atom&v=3.0&oauth_token='.$access_token;
        $url = 'http://www.google.com/m8/feeds/contacts/default/full/a9eb4b8d78c108?&alt=json&v=3.0&oauth_token='.$access_token;




        $response =  file_get_contents($url);
        echo $response;

	}

	/**
	 * Checks configuration values used by this command
	 *
	 * @throws \LogicException
	 */
	protected function checkConfiguration() {
		//
	}
}