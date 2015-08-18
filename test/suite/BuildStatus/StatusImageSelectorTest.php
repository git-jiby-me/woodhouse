<?php

namespace Icecave\Woodhouse\BuildStatus;

use PHPUnit_Framework_TestCase;

class StatusImageSelectorTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->selector = new StatusImageSelector();
    }

    public function testImageFilename()
    {
        $this->assertSame('build-status-passing.png', $this->selector->imageFilename(BuildStatus::PASSING()));
        $this->assertSame('build-status-failing.png', $this->selector->imageFilename(BuildStatus::FAILING()));
        $this->assertSame('build-status-pending.png', $this->selector->imageFilename(BuildStatus::PENDING()));
        $this->assertSame('build-status-unknown.png', $this->selector->imageFilename(BuildStatus::UNKNOWN()));
        $this->assertSame('build-status-error.png',   $this->selector->imageFilename(BuildStatus::ERROR()));
    }
}
