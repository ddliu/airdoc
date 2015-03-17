<?php
/**
 * Airdoc
 * @author Liu Dong <ddliuhb@gmail.com>
 * @license MIT
 * @copyright 2015 Liu Dong
 */

use ddliu\airdoc\Filesystem;

class FilesystemTest extends PHPUnit_Framework_TestCase {
    protected $fs;
    public function setup() {
        $fs = new Filesystem();
        $fs->mount(__DIR__.'/fs/a', '/doc/');
        $fs->mount(__DIR__.'/fs/b', '/doc/');

        $this->fs = $fs;
    }

    public function testResolveRelative() {
        $base = '/doc/';
        $tests = [
            '/' => false,
            '/doc/' => '',
            '/doc' => '',
            '/do' => false,
            '/doc/aa' => 'aa',
            '/doc/aa/' => 'aa/',
            '/doc/b.txt' => 'b.txt',
            '/doc/aa/b.txt' => 'aa/b.txt',
        ];

        foreach ($tests as $path => $expected) {
            $result = $this->fs->resolveRelative($base, $path);
            $this->assertEquals($expected, $result, $path.' => '.$expected);
        }
    }

    public function testSearch() {
        $result = $this->fs->search('/doc/1.txt');
        $this->assertCount(2, $result);
        $this->assertStringEndsWith('/a', $result[0]->getPath());
        $this->assertStringEndsWith('/b', $result[1]->getPath());

        $result = $this->fs->search('/doc/1.txt', 1);
        $this->assertCount(1, $result);

        $result = $this->fs->search('/doc/c');
        $this->assertCount(1, $result);
        $this->assertStringEndsWith('/a', $result[0]->getPath());

        $this->assertEmpty($this->fs->search('/doc/999.txt'));
    }

    public function testChildren() {
        $result = $this->fs->getChildren('/doc/');
        $this->assertCount(5, $result);

        $result = array_map(function($v) {
            return $v->getFileName();
        }, $result);

        sort($result);

        $expected = ['c', 'd', '1.txt', '2.txt', '3.txt'];
        sort($expected);

        $this->assertEquals($expected, $result);
    }
}