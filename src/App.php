<?php
/**
 * Airdoc
 * @author Liu Dong <ddliuhb@gmail.com>
 * @license MIT
 * @copyright 2015 Liu Dong
 */

namespace ddliu\airdoc;
use ddliu\airdoc\Auth\CallbackAuth;
use ddliu\airdoc\Auth\BasicAuth;
use ddliu\template\Engine as TemplateEngine;

class App {
    protected $configs;
    protected $fs;
    public function __construct($configs = array()) {
        $this->configs = array_merge($this->getDefaults(), $configs);
        $fs = new Filesystem();
        if (is_array($this->configs['mount'])) {
            foreach ($this->configs['mount'] as $src => $target) {
                $fs->mount($src, $target);
            }
        }

        $this->fs = $fs;
    }

    protected function getDefaults() {
        return [
            'template' => __DIR__.'/../template',
        ];
    }

    public function auth() {
        if (!empty($this->configs['custom_auth'])) {
            $auth = new CallbackAuth($this->configs['custom_auth']);
        } elseif (!empty($this->configs['users'])) {
            $auth = new BasicAuth($this->configs['users']);
        } else {
            return true;
        }

        return $auth->auth();
    }

    public function run() {
        $this->auth();

        list($root, $path) = Util::getUrlInfo();
        if ($this->ignore($path)) {
            return $this->notFound();
        }

        $currentInfo = $this->fs->search($path, 1);
        if (!$currentInfo) {
            return $this->notFound();
        }

        $currentInfo = $currentInfo[0];

        if ($currentInfo->isDir()) {
            $this->handleDir($path, $currentInfo);
        } elseif ($currentInfo->isFile()) {
            if (MimeType::isMarkdown($currentInfo->getFilename())) {
                $this->handleMarkdown($path, $currentInfo);
            } else {
                $this->handleStatic($path, $currentInfo);
            }
        } else {
            $this->notFound();
        }
    }

    protected function render($tpl, $variables = []) {
        $engine = new TemplateEngine($this->configs['template']);
        $engine->render($tpl, $variables);
    }

    protected function handleDir($path, $dirInfo) {
        if (substr($path, -1) !== '/') {
            header('location: '.$path.'/');
            return;
        }
        $children = $this->fs->getChildren($path);

        $title = $dirInfo->getBasename().' - '.$this->configs['title'];

        $this->render('dir.php', [
            'title' => $title,
            'breadcrumb' => $this->getBreadcrumb($path),
            'children' => $children,
        ]);
    }

    protected function handleStatic($path, $currentInfo) {
        $mimetype = MimeType::getMimeType($path);
        header('Content-Type: '.$mimetype);
        readfile($currentInfo);
    }

    protected function handleMarkdown($path, $currentInfo) {
        $extra = new \ParsedownExtra();
        $content = $extra->text(file_get_contents($currentInfo));

        $title = $currentInfo->getFilename().' - '.$this->configs['title'];

        $this->render('markdown.php', [
            'title' => $title,
            'breadcrumb' => $this->getBreadcrumb($path),
            'content' => $content,
        ]);
    }

    protected function getBreadcrumb($path) {
        $path = trim($path, '/');
        $breadcrumb = array(array('path' => '/', 'name' => 'Home'));
        $current = '/';
        foreach(explode('/', $path) as $name) {
            if ($name === '') {
                continue;
            }

            $current .= $name.'/';
            $breadcrumb[] = array(
                'path' => $current, 
                'name' => $name
            );
        }

        return $breadcrumb;
    }

    public function notFound() {
        header('HTTP/1.1 404 Not Found');
        $this->render('404.php');
    }

    protected function ignore($path) {
        foreach ($this->configs['ignore_regexp'] as $regexp) {
            $regexp = '#'.$regexp.'#';
            if (preg_match($regexp, $path)) {
                return true;
            }
        }

        return false;
    }
}