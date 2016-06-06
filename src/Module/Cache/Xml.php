<?php
namespace Language\Module\Cache;
use Language\Exception as E;

class Xml extends AbsCache
{
    protected $path = 'flash/';

    public function getFileName()
    {
        return  "{$this->getStorage()}/lang_{$this->language}.xml";
    }

    protected function getXml($content)
    {
        if (!$content) {
            throw new E\WrongParam('null content');
        }

        $xml = simplexml_load_string($content);
        if (!$xml) {
            throw new E\WrongParam('invalid cache XML content');
        }
        
        return $xml;
    }

    protected function checkBuildContent(\SimpleXMLElement $xml)
    {
        if (!$this->is_empty_allow && !$xml->count()) {
            throw new E\WrongParam('empty cache XML content');
        }
        
        return $this;
    }
    public function build()
    {
        $xml = $this->getXml(parent::build());
        $this->checkBuildContent($xml);
        
        return $xml;    
    }
    
    public function checkContent($data)
    {
        parent::checkContent($data);
        $this->checkBuildContent(
            $this->getXml($data)
        );

        return $this;
    }
}