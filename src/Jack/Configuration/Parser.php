<?php
/**
 * Created by PhpStorm.
 * User: jackisback
 * Date: 22/05/15
 * Time: 0.27
 */

namespace Jack\Configuration;

use \Symfony\Component\Yaml\Parser as YamlParser;

use Symfony\Component\Filesystem\Filesystem;

class Parser {
    /**
     * @param $config_file
     * @return mixed
     */
    public function getConfiguration($config_file)
    {
        $fs = new Filesystem();
        if (!$fs->exists($config_file)) {
            throw new \InvalidArgumentException("The configuration file does not exist!
            Maybe, you forgot to create private/gapp.yml file?");
        }

        $yamlParser = new YamlParser();
        $config = $yamlParser->parse(file_get_contents($config_file));

        if (!is_array($config) || !isset($config["config"])) {
            throw new \InvalidArgumentException("Malformed configuration file! Missing 'config' root node!");
        }
        $config = $config["config"];

        return $config;
    }
}