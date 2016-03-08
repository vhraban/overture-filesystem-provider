<?php
namespace Overture\FileSystemProvider;

use Overture\Exception\MissingKeyException;
use Overture\Exception\UnexpectedValueException;
use Overture\FileSystemProvider\Exception\MalformedYamlException;
use Overture\OvertureProviderInterface;
use Overture\ProviderFoundation\TraversableValueContainer;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Parser;

class YamlProvider implements OvertureProviderInterface
{
    /** @var TraversableValueContainer Data container */
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
        $arrayContainer = $parser->parse($contents);

        if(!is_array($arrayContainer))
        {
            throw new MalformedYamlException("File is malformed");
        }

        $this->valueContainer = new TraversableValueContainer($arrayContainer);
    }

    /**
     * Get a value for a configuration key
     *
     * @param string $key The configuration key
     *
     * @throws MissingKeyException if the key is missing
     * @throws UnexpectedValueException if the returned value is not scalar
     *
     * @return string
     */
    public function get($key)
    {
        return $this->valueContainer->get($key);
    }
}
