<?php
namespace Icecave\Woodhouse\Coverage;

class CoverageImageSelector
{
    /**
     * @param integer $increments
     */
    public function __construct($increments = 5)
    {
        $this->increments = $increments;
    }

    /**
     * @param float $percentage The actual percentage coverage.
     *
     * @return integer The coverage percentage rounded do the nearest 5%.
     */
    public function roundPercentage($percentage)
    {
        return intval($percentage - $percentage % $this->increments);
    }

    /**
     * @param float $percentage The actual percentage coverage.
     *
     * @return string The filename of the image to use.
     */
    public function imageFilename($percentage)
    {
        $percentage = $this->roundPercentage($percentage);

        return sprintf('test-coverage-%03d.png', $percentage);
    }

    /**
     * @return string The filename of the "error" image.
     */
    public function errorImageFilename()
    {
        return 'test-coverage-error.png';
    }

    /**
     * @return string The filename of the "unknown" image.
     */
    public function unknownImageFilename()
    {
        return 'test-coverage-unknown.png';
    }

        private $increments;
}
