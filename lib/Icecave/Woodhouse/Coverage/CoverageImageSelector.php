<?php
namespace Icecave\Woodhouse\Coverage;

use Icecave\Woodhouse\TypeCheck\TypeCheck;

class CoverageImageSelector
{
    /**
     * @param integer $increments
     */
    public function __construct($increments = 5)
    {
        $this->typeCheck = TypeCheck::get(__CLASS__, func_get_args());

        $this->increments = $increments;
    }

    /**
     * @param float $percentage The actual percentage coverage.
     *
     * @return integer The coverage percentage rounded do the nearest 5%.
     */
    public function roundPercentage($percentage)
    {
        $this->typeCheck->roundPercentage(func_get_args());

        return intval($percentage - $percentage % $this->increments);
    }

    /**
     * @param float $percentage The actual percentage coverage.
     *
     * @return string The filename of the image to use.
     */
    public function imageFilename($percentage)
    {
        $this->typeCheck->imageFilename(func_get_args());

        $percentage = $this->roundPercentage($percentage);

        return sprintf('test-coverage-%03d.png', $percentage);
    }

    /**
     * @return string The filename of the "error" image.
     */
    public function errorImageFilename()
    {
        $this->typeCheck->errorImageFilename(func_get_args());

        return 'test-coverage-error.png';
    }

    /**
     * @return string The filename of the "unknown" image.
     */
    public function unknownImageFilename()
    {
        $this->typeCheck->unknownImageFilename(func_get_args());

        return 'test-coverage-unknown.png';
    }

    private $typeCheck;
    private $increments;
}
