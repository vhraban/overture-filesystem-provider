<?php
namespace Overture\FileSystemProvider;

use Overture\FileSystemProvider\Exception\InaccessibleFileException;
use UnexpectedValueException;

class FileResource
{
    /**
     * @var string Raw contents of the file
     */
    protected $rawContents;

    /**
     * Initialise the Resource
     *
     * @param string $source Path to the source file
     *
     * @throws InaccessibleFileException If file can not be accessed
     * @throws UnexpectedValueException If has dirty (for instance binary) data instead of printable characters
     */
    public function __construct($source)
    {
        // Suppressing warnings is bad, but in this case checking the value
        // returned from file_get_contents is enough
        $contents = @file_get_contents($source);

        if(false === $contents)
        {
            throw new InaccessibleFileException("Can not read configuration file {$source}");
        }

        if(!$this->isTextFile($source))
        {
            throw new UnexpectedValueException("{$source} does not seem to be text");
        }

        $this->rawContents = $contents;
    }

    /**
     * Check the file is of mime type text
     *
     * A bit of black magic here, check that mime type starts with text
     * and rely of that. But hey, RFC 2045 is 20 years old! Should be safe to
     * trust.
     *
     * @param string $path Path to the file
     *
     * @return bool
     */
    protected function isTextFile($path)
    {
        $fInfo = finfo_open(FILEINFO_MIME);

        $ret = substr(finfo_file($fInfo, $path), 0, 4) == 'text';

        finfo_close($fInfo);

        return $ret;

    }

    /**
     * Get raw contents of the fileResource
     *
     * @return string
     */
    public function getRawContents()
    {
        return $this->rawContents;
    }
}
