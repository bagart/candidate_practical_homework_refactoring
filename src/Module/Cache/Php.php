<?php

namespace Language\Module\Cache;
use Language\Exception as E;
use Language\Iface;

class Php extends AbsCache
{
    /**
     * @todo bad path
     * @var null
     */
    protected $path = null;
    
    protected $application;

    public function __construct($language, $application = null)
    {
        parent::__construct($language);
        $this->checkNodeName($application, '$application');
        $this->application = $application;
    }
    
    public function getFileName()
    {
        return  "{$this->getStorage()}/{$this->application}/{$this->language}.php";
    }

    public function checkBuildContent(array $content)
    {
        if (!$this->is_empty_allow && !count($content)) {
            throw new E\WrongParam('empty cache PHP content');
        }
        
        return $this;
    }

    public function build()
    {
        $result = null;
        try {
            $result = include($this->getFileName());
        } catch (Iface\LanguageException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new E\WrongParam(get_class($e) . " while check cache PHP file content: {$this->getFileName()}");
        }
        if (!is_array($result)) {
            throw new E\WrongParam('not array cache PHP content');
        }
        
        $this->checkBuildContent($result);
        
        return $result;
    }

}