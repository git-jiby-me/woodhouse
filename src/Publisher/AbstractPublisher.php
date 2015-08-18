<?php

namespace Icecave\Woodhouse\Publisher;

abstract class AbstractPublisher implements PublisherInterface
{
    public function __construct()
    {
        $this->contentPaths = array();
    }

    /**
     * Enqueue content to be published.
     *
     * @param string $sourcePath
     * @param string $targetPath
     */
    public function add($sourcePath, $targetPath)
    {
        $this->contentPaths[$sourcePath] = ltrim($targetPath, '/');
    }

    /**
     * Remove enqueued content at $sourcePath.
     *
     * @param string $sourcePath
     */
    public function remove($sourcePath)
    {
        unset($this->contentPaths[$sourcePath]);
    }

    /**
     * Clear all enqueued content.
     */
    public function clear()
    {
        $this->contentPaths = array();
    }

    /**
     * @return array<string, string>
     */
    public function contentPaths()
    {
        return $this->contentPaths;
    }

    private $contentPaths;
}
