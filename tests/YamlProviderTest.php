<?php
namespace Overture\FileSystemProvider\Tests;

use Overture\Exception\MissingKeyException;
use Overture\Exception\UnexpectedValueException;
use Overture\FileSystemProvider\Exception\InaccessibleFileException;
use Overture\FileSystemProvider\Exception\MalformedYamlException;
use Overture\FileSystemProvider\FileResource;
use Overture\FileSystemProvider\YamlProvider;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;

class YamlProviderTest extends PHPUnit_Framework_TestCase
{
    const TEST_YML_FILENAME = "/tmp/testfile.yml";

    /**
     * Mock a FileResource with YML contents
     *
     * @param string $fileContents Optionally override default contents
     *
     * @return PHPUnit_Framework_MockObject_MockObject|FileResource
     */
    protected function mockYmlFileResource($fileContents = null)
    {
        $contents = $fileContents ? $fileContents : <<<EOD
parameter:
  array:
    - a
    - b
  one: 1
  two: 2
EOD;

        $mock = $this->getMockBuilder(FileResource::class)
                        ->disableOriginalConstructor()
                        ->getMock();

        $mock->expects($this->once())
                ->method("getRawContents")
                ->will($this->returnValue($contents));

        return $mock;
    }

    public function testFullRun()
    {
        $fileProvider = $this->mockYmlFileResource();

        $provider = new YamlProvider($fileProvider);
        $actualValue = $provider->get("parameter.one");
        $this->assertEquals("1", $actualValue);

    }

    public function testMissingKeyException()
    {
        $fileProvider = $this->mockYmlFileResource();

        $this->setExpectedException(MissingKeyException::class);

        $provider = new YamlProvider($fileProvider);
        $provider->get("parameter.three");
    }

    public function testUnexpectedValueException()
    {
        $fileProvider = $this->mockYmlFileResource();

        $this->setExpectedException(UnexpectedValueException::class);

        $provider = new YamlProvider($fileProvider);
        $provider->get("parameter.array");

    }

    public function testMalformedYamlException()
    {
        $fileProvider = $this->mockYmlFileResource("I am not YAML");

        $this->setExpectedException(MalformedYamlException::class);

        new YamlProvider($fileProvider);

    }
}
