<?php
namespace Overture\FileSystemProvider\Tests;

use Overture\FileSystemProvider\Exception\InaccessibleFileException;
use Overture\FileSystemProvider\FileResource;
use PHPUnit_Framework_TestCase;
use UnexpectedValueException;

class FileResourceTest extends PHPUnit_Framework_TestCase
{
    const TEST_YML_FILENAME = "/tmp/testfile.yml";

    protected function createTestYaml($fileContents = null)
    {
        // First of all, try to delete the old file in case
        // in it present
        $this->deleteTestYml();

        $contents = $fileContents ? $fileContents : <<<EOD
parameter:
  array:
    - a
    - b
  one: 1
  two: 2
EOD;

        file_put_contents(static::TEST_YML_FILENAME, $contents);
    }

    protected function deleteTestYml()
    {
        @unlink(static::TEST_YML_FILENAME);
    }

    public function testFileDoesNotExist()
    {
        $this->setExpectedException(InaccessibleFileException::class);

        new FileResource("non-existent.file");
    }

    public function testBinaryFile()
    {
        $this->setExpectedException(UnexpectedValueException::class);

        new FileResource("/usr/bin/env");
    }

    public function testRawContents()
    {
        $this->createTestYaml();

        $fileResource = new FileResource(static::TEST_YML_FILENAME);

        $expected = <<<EOD
parameter:
  array:
    - a
    - b
  one: 1
  two: 2
EOD;
        $this->assertEquals($expected, $fileResource->getRawContents());

        $this->deleteTestYml();
    }
}
