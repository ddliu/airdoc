<?php
class Airdoc {
    protected $config;
    public function __construct($config = array()) {
        $this->config = $config;
    }

    public function serve() {
        list($rootUrl, $path) = $this->getUrlInfo();

        $src = $this->find($path);

        if (!$src) {

        }
    }

    public function isStatic($path) {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        return in_array($ext, array('css', 'js', 'jpg', 'jpeg', 'png', 'svg', 'txt'));
    }

    public function isMarkdown($path) {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        return in_array($ext, array('md', 'markdown'));
    }

    public function serveStatic($srcPath) {
        readfile($srcPath);
    }

    public function serve404() {

    }

    public function serveMarkdown($path) {
        require_once dirname(__FILE__).'/Parsedown.php';
        $parsedown = new Parsedown();
        $body = $parsedown->text(file_get_contents($path));

        echo $body;
    }

    public function find($path) {
        foreach ($this->config['mount'] as $src => $target) {
            if ($src === '') {
                $targetPath = $target.'/'.$path;
            } elseif ($path === $src) {
                $targetPath = $target;
            } elseif (strpos($path, $src . '/') === 0 ) {
                $targetPath = $target . '/'. substr($path, strlen($src) + 1);
            }

            // normalize
            $targetPath = rtrim($targetPath, '/');

            if (is_dir($targetPath) || is_file($targetPath)) {
                return $targetPath;
            }
        }

        return false;
    }

    public function mount($src, $target) {

    }

    public function getUrlInfo() {
        $rootUrl = '/';
        $path = 'a/b/c/d.md';

        return array($rootUrl, $path);
    }

}