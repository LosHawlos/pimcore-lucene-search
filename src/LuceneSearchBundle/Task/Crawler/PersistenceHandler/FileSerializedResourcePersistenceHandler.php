<?php

namespace LuceneSearchBundle\Task\Crawler\PersistenceHandler;

use VDB\Spider\PersistenceHandler\PersistenceHandlerInterface;
use VDB\Spider\PersistenceHandler\FilePersistenceHandler;
use VDB\Spider\Resource;

class FileSerializedResourcePersistenceHandler extends FilePersistenceHandler implements PersistenceHandlerInterface
{
    /**
     *
     * The path that was provided with a default filename appended if it is
     * a path ending in a / or if it's not a file. This is because we don't want to persist
     * the directories as files. This is similar to wget behaviour.
     *
     * @param string $path
     *
     * @return string
     */
    protected function completePath($path): string
    {
        if (substr($path, -1, 1) === '/') {
            $path .= $this->defaultFilename;
        } else {
            $pathFragments = explode('/', $path);
            if (strpos(end($pathFragments), '.') === false) {
                $path .= '/' . $this->defaultFilename;
            }
        }

        return $path;
    }

    /**
     * @param Resource $resource
     */
    public function persist(Resource $resource)
    {
        $path = rtrim($this->getResultPath() . $this->getFileSystemPath($resource), '/');
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        $file = new \SplFileObject($path . DIRECTORY_SEPARATOR . $this->getFileSystemFilename($resource), 'w');
        $this->totalSizePersisted += $file->fwrite(serialize($resource));
    }

    /**
     * @return Resource
     */
    public function current(): Resource
    {
        return unserialize($this->getIterator()->current()->getContents());
    }
}
