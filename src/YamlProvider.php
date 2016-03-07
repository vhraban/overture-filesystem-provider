<?php
namespace Overture\FileSystemProvider;

use Overture\Exception\MissingKeyException;
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
     * @param FileResource $fileResource Initialsed FileResource
     *
     * @throws ParseException If yaml file can not be parsed
     * @throws MalformedYamlException if yaml file is malformed
     */
    public function __construct(FileResource $fileResource)
    {
        $contents = $fileResource->getRawContents();
        $parser = new Parser();
        $this->valueContainer = $parser->parse($contents);

        if(!is_array($this->valueContainer))
        {
            throw new MalformedYamlException("File is malformed");
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
