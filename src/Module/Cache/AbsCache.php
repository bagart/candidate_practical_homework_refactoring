<?php

namespace Language\Module\Cache;
use Language\Config;
use Language\Exception as E;

abstract class AbsCache
{
    protected $storage = '/cache/';
    protected $is_empty_allow= true;

    protected $path = null;
    protected $language = null;
    
    public function __construct($language)
    {
        Config::get('system.paths.root');
        
        if (Config::get('system.paths.root')) {
            $this->setStorage(Config::get('system.paths.root') . $this->storage);
        }
        $this->language = $language;
        $this->checkNodeName($language, '$language');
    }

    public function setStorage($storage)
    {
        $this->storage = $storage;
        
        return $this;
    }

    public function getStorage()
    {
        return $this->storage . $this->path;
    }

    public function drop()
    {
        if (!file_exists($this->getFileName())) {
            throw new E\WrongParam("ERROR: !file_exists: {$this->getFileName()}");
        }
        if (!unlink($this->getFileName())) {
            throw new E\ErrorResult('ERROR: unlink cache PHP content');
        }
    }
    
    protected function checkNodeName($node, $description = 'node')
    {
        if (!preg_match('~^[^/]+$~u', $node)) {
            throw new E\WrongParam("invalid {$description}: $node");
        }

        return $this;
    }
    
    public function store($content)
    {
        $this->checkContent($content);
        $dir = dirname($this->getFileName());
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
            if (!is_dir($dir)) {
                throw new E\WrongParam("mkdir cache path error: {$dir}");
            }
        }

        if (!file_put_contents($this->getFileName(), $content)) {
            throw new E\WrongParam("file_put_contents error: {$this->getFileName()}");
        }
        
        if (Config::get('debug')) {
            //@todo debug check
            $result = $this->read();
            if ($content != $result) {
                throw new E\WrongParam("store != read");
            }
            
            $this->build();
        }
        return $this;
    }

    public function read()
    {
        $content = file_get_contents($this->getFileName());
        $this->checkContent($content);

        return $content;
    }
    
    public function build()
    {
        return $this->read();
    }

    public function checkContent($data)
    {
        if (!$data) {
            throw new E\WrongParam('empty content');
        }

        return $this;
    }

    abstract public function getFileName();
}