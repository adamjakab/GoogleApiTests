<?php
namespace Jack\Console\Command;

use Symfony\Component\Console\Command\Command as ConsoleCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Parser;

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
    public function __construct($name = null)
    {
        parent::__construct($name);
    }

    protected function _execute(InputInterface $input, OutputInterface $output)
    {
        $this->cmdInput = $input;
        $this->cmdOutput = $output;
        $this->parseConfiguration();
    }

    /**
     * Parse yml configuration
     * @todo: use Jack\Configuration\Parser
     */
    protected function parseConfiguration()
    {
        $config_file = $this->cmdInput->getArgument('config_file');
        if (!file_exists($config_file)) {
            throw new \InvalidArgumentException("The configuration file does not exist!");
        }
        $yamlParser = new Parser();
        $config = $yamlParser->parse(file_get_contents($config_file));
        if (!is_array($config) || !isset($config["config"])) {
            throw new \InvalidArgumentException("Malformed configuration file!");
        }
        $this->config = $config["config"];

        //do some other checks

    }

    /**
     * @param string $msg
     */
    protected function log($msg)
    {
        $this->cmdOutput->writeln($msg);
    }
}