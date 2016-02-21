<?php
namespace Overture\FileSystemProvider\Tests;

use Overture\Exception\MissingKeyException;
use Overture\Exception\UnexpectedValueException;
use Overture\FileSystemProvider\Exception\InaccessibleFileException;
use Overture\FileSystemProvider\YamlProvider;
use PHPUnit_Framework_TestCase;

class YamlProviderTest extends PHPUnit_Framework_TestCase
{
    const TEST_YML_FILENAME = "/tmp/testfile.yml";

    protected function createTestYaml()
    {
        $contents = "parameter:
  array:
    - a
    - b
  one: 1
  two: 2
";

        file_put_contents(static::TEST_YML_FILENAME, $contents);
    }

    protected function deleteTestYml()
    {
        unlink(static::TEST_YML_FILENAME);
    }

    public function testFullRun()
    {
        $this->createTestYaml();

        $provider = new YamlProvider(static::TEST_YML_FILENAME);
        $actualValue = $provider->get("parameter.one");
        $this->assertEquals("1", $actualValue);

        $this->deleteTestYml();
    }

    public function testInaccessibleFileException()
    {
        $this->setExpectedException(InaccessibleFileException::class);

        new YamlProvider("/tmp/nonexistant_file.yml");
    }

    public function testMissingKeyException()
    {
        $this->createTestYaml();

        $this->setExpectedException(MissingKeyException::class);

        $provider = new YamlProvider(static::TEST_YML_FILENAME);
        $provider->get("parameter.three");

        $this->deleteTestYml();
    }

    public function testUnexpectedValueException()
    {
        $this->createTestYaml();

        $this->setExpectedException(UnexpectedValueException::class);

        $provider = new YamlProvider(static::TEST_YML_FILENAME);
        $provider->get("parameter.array");

        $this->deleteTestYml();
    }
}
