<?php
namespace Icecave\Woodhouse\BuildStatus;

use PHPUnit_Framework_TestCase;

class StatusImageSelectorTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->_selector = new StatusImageSelector;
    }

    public function testImageFilename()
    {
       $this->assertSame('build-status-passing.png', $this->_selector->imageFilename(BuildStatus::PASSING()));
       $this->assertSame('build-status-failing.png', $this->_selector->imageFilename(BuildStatus::FAILING()));
       $this->assertSame('build-status-pending.png', $this->_selector->imageFilename(BuildStatus::PENDING()));
       $this->assertSame('build-status-unknown.png', $this->_selector->imageFilename(BuildStatus::UNKNOWN()));
       $this->assertSame('build-status-error.png',   $this->_selector->imageFilename(BuildStatus::ERROR()));
    }
}
