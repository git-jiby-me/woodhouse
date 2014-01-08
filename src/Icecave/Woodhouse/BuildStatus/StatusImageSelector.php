<?php
namespace Icecave\Woodhouse\BuildStatus;

use Icecave\Woodhouse\TypeCheck\TypeCheck;

class StatusImageSelector
{
    public function __construct()
    {
        $this->typeCheck = TypeCheck::get(__CLASS__, func_get_args());
    }

    /**
     * @param BuildStatus $status The build status.
     *
     * @return string The filename of the image to use.
     */
    public function imageFilename(BuildStatus $status)
    {
        $this->typeCheck->imageFilename(func_get_args());

        return sprintf('build-status-%s.png', $status->value());
    }

    private $typeCheck;
    private $increments;
}
