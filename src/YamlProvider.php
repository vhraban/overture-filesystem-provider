<?php
namespace Overture\FileSystemProvider;

use Overture\Exception\MissingKeyException;
use Overture\FileSystemProvider\Exception\InaccessibleFileException;
use Overture\FileSystemProvider\Exception\MalformedYamlException;
use Overture\OvertureProviderInterface;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Parser;
use Overture\Exception\UnexpectedValueException;


class YamlProvider implements OvertureProviderInterface
{
    const KEY_NODE_DELIMITER = ".";

    /** @var array Value container */
    protected $valueContainer;

    /**
     * Provider constructor.
     * Sets up the value container by reading the yaml file
     * from a given path
     *
     * @param string $yamlPath A path to Yaml file
     *
     * @throws ParseException If yaml file can not be parsed
     * @throws MalformedYamlException if yaml file is malformed
     */
    public function __construct($yamlPath)
    {
        $parser = new Parser();
        $fileContents = $this->readfile($yamlPath);
        $this->valueContainer = $parser->parse($fileContents);

        if(!is_array($this->valueContainer))
        {
            throw new MalformedYamlException("{$yamlPath} is malformed");
        }

    }

    /**
     * Get a value for a configuration key
     *
     * @param string $key The configuration key
     *
     * @throws MissingKeyException if the path is unreachable
     * @throws UnexpectedValueException if the path is key is missing
     *
     * @return string
     */
    public function get($key)
    {
        $ret = $this->traverseValueContainer($key);
        if(!is_scalar($ret))
        {
            throw new UnexpectedValueException("The {$key} configuration value is not scalar");
        }
        return $ret;
    }

    /**
     * Read a file contents from a given path
     * A simple wrapper around file_get_content
     *
     * @param string $filePath File path to read
     *
     * @throws InaccessibleFileException if the file can not be read
     *
     * @return string
     */
    protected function readFile($filePath)
    {
        // Supressing warnings is bad, but in this case checking the value
        // returned from file_get_contents is enough

        $contents = @file_get_contents($filePath);
        if(false === $contents)
        {
            throw new InaccessibleFileException("Can not read configuration file {$filePath}");
        }
        return $contents;
    }

    /**
     * Traverse a value container until the end of path is reached
     *
     * $path can be separated with a dot .
     *
     * In an array [ "section" => ["a => "b"], ["b" => "B"]
     *
     * value B can be accessed by giving section.b key
     *
     * @param string $path The path
     *
     * @throws MissingKeyException if the path is unreachable
     *
     * @return string
     */
    protected function traverseValueContainer($path)
    {
        // before the travel current node is the root
        $currentNode = $this->valueContainer;

        $pathElements = explode(static::KEY_NODE_DELIMITER, $path);
        foreach($pathElements as $node)
        {
            // Ensure the next child exists
            if(!isset($currentNode[$node]))
            {
                throw new MissingKeyException("{$path} configuration value is not found");
            }

            //update the current path if the child existed
            $currentNode = $currentNode[$node];
        }

        return $currentNode;
    }
}
