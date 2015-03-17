<?php
/**
 * Airdoc
 * @author Liu Dong <ddliuhb@gmail.com>
 * @license MIT
 * @copyright 2015 Liu Dong
 */

namespace ddliu\airdoc;

/**
 * File system with multiple sources.
 */
class Filesystem {
    protected $mountList = [];
    public function __construct($base = null) {
        if ($base !== null) {
            $this->mount($base, '/');
        }
    }

    /**
     * Mount $src to $target
     * @param  string $src
     * @param  string $target
     */
    public function mount($src, $target) {
        $this->mountList[$src] = $target;
    }


    public function getChildren($path) {
        $result = [];
        foreach ($this->mountList as $src => $target) {
            if (($relative = $this->resolveRelative($target, $path)) !== false) {
                $srcPath = $this->joinPaths($src, $relative);
                if (is_dir($srcPath)) {
                    $children = $this->getDirectoryChildren($srcPath);
                    if ($children) {
                        foreach ($children as $child) {
                            if (!isset($result[$child])) {
                                $result[$child] = new \SplFileInfo($this->joinPaths($srcPath, $child));
                            }
                        }
                    }
                }
            }
        }

        return array_values($result);
    }

    public function search($path, $limit = 0) {
        $result = [];
        foreach ($this->mountList as $src => $target) {
            if (($relative = $this->resolveRelative($target, $path)) !== false) {
                $srcPath = $this->joinPaths($src, $relative);
                $info = new \SplFileInfo($srcPath);
                if ($info->isFile() || $info->isDir()) {
                    $result[] = $info;
                    if ($limit > 0) {
                        $limit--;
                        if ($limit <= 0) {
                            break;
                        }
                    }
                }
            }
        }

        return $result;
    }

    public function resolveRelative($mountPoint, $path) {
        // mountPoint path should ends with /
        if (substr($mountPoint, -1) !== '/') {
            $mountPoint .= '/';
        }

        if (strpos($path, $mountPoint) !== 0) {
            return false;
        }

        if ($path === $mountPoint) {
            return '';
        }

        $relative = substr($path, strlen($mountPoint));
        return $relative;
    }

    protected function joinPaths() {
        $paths = array();

        foreach (func_get_args() as $arg) {
            if ($arg !== '') { $paths[] = $arg; }
        }

        return preg_replace('#/+#','/',join('/', $paths));
    }

    protected function getDirectoryChildren($dir) {
        if (!($dirh = opendir($dir))) {
            return false;
        }

        $result = [];
        while (($file = readdir($dirh)) !== false) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $result[] = $file;
        }

        return $result;
    }
}