<?php
/**
 * @author Liu Dong <ddliuhb@gmail.com>
 * @license MIT
 * @copyright 2015 Liu Dong
 */
class Airdoc {
    protected $config;
    public function __construct($config = array()) {
        $this->config = $config;
    }

    public function isIgnore($path) {
        $name = $this->getBasename($path);
        if ($name[0] === '.') {
            return true;
        }

        return false;
    }

    public function serve() {
        list($rootUrl, $path) = $this->getUrlInfo();

        $src = $this->find($path);

        if (!$src) {
            $this->serve404();
        } else {
            if (is_dir($src)) {
                $this->serveDir($path, $src);
            } elseif ($this->isMarkdown($src)) {
                $this->serveMarkdown($path, $src);
            } elseif ($this->isStatic($src)) {
                $this->serveStatic($src);
            } else {
                $this->serve404();
            }
        }
    }

    public function isStatic($path) {
        $ext = strtolower($this->getExt($path));
        return in_array($ext, array('css', 'js', 'jpg', 'jpeg', 'png', 'svg', 'txt'));
    }

    public function isMarkdown($path) {
        $ext = strtolower($this->getExt($path));
        return in_array($ext, array('md', 'markdown'));
    }

    public function serveStatic($srcPath) {
        $contentType = $this->getMimeType($srcPath);
        header('Content-Type: '.$contentType);
        readfile($srcPath);
    }

    public function serve404() {
        header('HTTP/1.1 404 Not Found');
        echo 'Not Found';
    }

    public function serveDir($path, $dir) {
        $dirh = opendir($dir);
        $files = array();
        $dirs = array();
        while (($file = readdir($dirh)) !== false) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            if ($this->isIgnore($file)) {
                continue;
            }

            if ($this->isMarkdown($file)) {
                $files[] = array(
                    'name' => $file,
                    'path' => $this->joinPath($path, $file)
                );
            } elseif (is_dir($dir.'/'.$file)) {
                $dirs[] = array(
                    'name' => $file,
                    'path' => $this->joinPath($path, $file)
                );
            }
        }
        closedir($dirh);
        $this->render('dir', array(
            'path' => $path,
            'breadcrumb' => $this->getBreadcrumb($path),
            'title' => $this->config['title'],
            'dirs' => $dirs,
            'files' => $files,
        ));
    }

    public function render($template, $values = array()) {
        extract($values);
        require(dirname(__FILE__).'/template/header.php');
        require(dirname(__FILE__).'/template/'.$template.'.php');
        require(dirname(__FILE__).'/template/footer.php');
    }

    public function serveMarkdown($path, $src) {
        require_once dirname(__FILE__).'/Parsedown.php';
        $parsedown = new Parsedown();
        $body = $parsedown->text(file_get_contents($src));

        $this->render('markdown', array(
            'title' => $this->config['title'],
            'path' => $path,
            'breadcrumb' => $this->getBreadcrumb($path),
            'content' => $body,
        ));
    }

    public function find($path) {
        foreach ($this->config['mount'] as $src => $target) {
            $relative = $this->resolveRelative($target, $path);
            if ($relative === false) {
                continue;
            }

            $srcPath = $this->joinPath($src, $relative);
            if (is_dir($srcPath) || is_file($srcPath)) {
                return $srcPath;
            }
        }

        return false;
    }

    public function resolveRelative($base, $path) {
        // base path should ends with /
        if (substr($base, -1) !== '/') {
            $base .= '/';
        }

        if (strpos($path, $base) !== 0) {
            return false;
        }

        if ($path === $base) {
            return '';
        }

        $relative = substr($path, strlen($base));
        return $relative;
    }

    private function getBreadcrumb($path) {
        $path = trim($path, '/');
        $breadcrumb = array(array('path' => '/', 'name' => 'Home'));
        $current = '';
        foreach(explode('/', $path) as $name) {
            if ($name === '') {
                continue;
            }

            $current .= '/'.$name;
            $breadcrumb[] = array(
                'path' => $current, 
                'name' => $name
            );
        }

        return $breadcrumb;
    }

    private function joinPath($a, $b) {
        return str_replace('//', '/', $a.'/'.$b);
    }

    private function getExt($path) {
        $dotpos = strrpos($path, '.');
        if ($dotpos === false) {
            return '';
        }

        return substr($path, $dotpos + 1);
    }

    private function getBasename($filename){
        return preg_replace('/^.+[\\\\\\/]/', '', $filename);
    }

    private function getMimeType($filename) {
        $mime_types = array(

            'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'php' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',

            // images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',

            // archives
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'exe' => 'application/x-msdownload',
            'msi' => 'application/x-msdownload',
            'cab' => 'application/vnd.ms-cab-compressed',

            // audio/video
            'mp3' => 'audio/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',

            // adobe
            'pdf' => 'application/pdf',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',

            // ms office
            'doc' => 'application/msword',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',

            // open office
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        );

        $ext = strtolower($this->getExt($filename));
        if (isset($mime_types[$ext])) {
            return $mime_types[$ext];
        }
        
        return 'application/octet-stream';
    }

    public function mount($src, $target) {

    }

    public function getUrlInfo() {
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $rootUrl = '/';
        $path = $path;
        if ($path === '') {
            $path = '/';
        }

        $path = urldecode($path);

        return array($rootUrl, $path);
    }

}