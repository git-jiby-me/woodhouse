<?php

namespace Icecave\Woodhouse\BuildStatus;

class StatusImageSelector
{
    /**
     * @param BuildStatus $status The build status.
     *
     * @return string The filename of the image to use.
     */
    public function imageFilename(BuildStatus $status)
    {
        return sprintf('build-status-%s.png', $status->value());
    }

    private $increments;
}
