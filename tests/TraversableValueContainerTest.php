<?php
namespace Overture\FileSystemProvider\Tests;

use Overture\Exception\MissingKeyException;
use Overture\Exception\UnexpectedValueException;
use Overture\FileSystemProvider\TraversableValueContainer;
use PHPUnit_Framework_TestCase;

class TraversableValueContainerTest extends PHPUnit_Framework_TestCase
{
    protected function getExampleTree()
    {
        return [
            "cats" => [
                "lion" => true,
                'leopard' => true,
                "hamster" => false
            ],
            "cameras" => [
                "nikon" => "good",
                "canon" => [
                    "digital" => "bad",
                    "film" => "good"
                ]
            ],
            "one" => 1,
            "two" => 2
        ];
    }

    public function testNodeNotFound()
    {
        $tree = $this->getExampleTree();
        $container = new TraversableValueContainer($tree);

        $this->setExpectedException(MissingKeyException::class);

        $container->get("cats.domestic");
    }

    public function testNonScalarValue()
    {
        $tree = $this->getExampleTree();
        $container = new TraversableValueContainer($tree);

        $this->setExpectedException(UnexpectedValueException::class);

        $container->get("cameras.canon");
    }

    public function testFullRun()
    {
        $tree = $this->getExampleTree();
        $container = new TraversableValueContainer($tree);

        $this->assertEquals("good", $container->get("cameras.canon.film"));
        $this->assertTrue($container->get("cats.lion"));
        $this->assertEquals(2, $container->get("two"));
    }
}
