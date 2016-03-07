<?php
namespace Overture\FileSystemProvider;

use Overture\Exception\MissingKeyException;
use Overture\Exception\UnexpectedValueException;


class TraversableValueContainer
{
    const KEY_NODE_DELIMITER = ".";

    /**
     * @var array The tree that is stored in an array representation
     */
    protected $tree;

    /**
     * Constructo
     *
     * @param array $tree
     */
    public function __construct(array $tree)
    {
        $this->tree = $tree;
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
        $ret = $this->traverseTree($key);
        if(!is_scalar($ret))
        {
            throw new UnexpectedValueException("The {$key} configuration value is not scalar");
        }
        return $ret;
    }

    /**
     * Traverse a tree until the end of path is reached
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
    protected function traverseTree($path)
    {
        // before the travel current node is the root
        $currentNode = $this->tree;

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
