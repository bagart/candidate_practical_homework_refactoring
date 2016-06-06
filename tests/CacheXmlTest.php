<?php

class CacheXmlTest extends PHPUnit_Framework_TestCase
{
    protected $mock_content = '<?xml version="1.0" encoding="UTF-8"?><data><foo value="bar"/></data>';

    protected  $filename_cache_xml = [
        'en' => 'cache/flash/lang_en.xml',
        //'hu' => 'cache/flash/lang_hu.xml',
    ];
    
    public function setUp()
    {
        foreach ($this->filename_cache_xml as $key => $path) {
            $this->filename_cache_xml[$key] = realpath($path);
        }
    }

    protected function getCacheXmlFiles()
    {
        return array_values($this->filename_cache_xml);
    }

    public function internal_testFile($filename)
    {
        $this->assertTrue(file_exists($filename));
        $this->assertTrue(filesize($filename) > 10);

        $content = file_get_contents($filename);
        $this->assertNotEmpty($content);
        $xml = simplexml_load_string($content);
        $this->assertNotEmpty($xml->count());
    }

    public function test_create()
    {
        try {
            new \Language\Module\Cache\Xml('test_lang');
            $this->assertTrue(true);
        } catch (\Language\Exception\WrongParam $e) {
            $this->assertTrue(false);
        }

        try {
            new \Language\Module\Cache\Php(null);
            $this->assertTrue(false);
        } catch (\Language\Exception\WrongParam $e) {
            $this->assertTrue(true);
        }

        try {
            new \Language\Module\Cache\Php('foo/bar');
            $this->assertTrue(false);
        } catch (\Language\Exception\WrongParam $e) {
            $this->assertTrue(true);
        }
    }

    
    public function test_real_files()
    {
        $file_list = $this->getCacheXmlFiles();
        $this->assertNotEmpty($file_list);

        $this->assertEquals(
            $file_list,
            array_unique(array_filter($file_list))
        );

        foreach ($file_list as $filename) {
            $this->internal_testFile($filename);
        }
    }

    public function test_store()
    {
        $test_path = __DIR__ . '/../cache/flash/lang_test_lang.xml';
        if (file_exists($test_path)) {
            unlink($test_path);
        }
        $this->assertFalse(file_exists($test_path));
        $cache_file = (new \Language\Module\Cache\Xml('test_lang'))
            ->store($this->mock_content);
        
        $this->assertEquals(
            realpath($cache_file->getFileName()),
            realpath($test_path)
        );

        $this->internal_testFile($cache_file->getFileName());
        try {
            $result = $cache_file->build();
            $foo = current($result->xpath('/data/foo'));
            /**
             * @var $foo \SimpleXMLElement
             */
            $this->assertTrue($foo instanceof \SimpleXMLElement);
            $this->assertEquals(current($foo->attributes()->value), 'bar');
            $this->assertEquals($foo->asXML(),'<foo value="bar"/>');
        } catch (\Language\Exception\WrongParam $e) {
            $this->assertTrue(false);
        }

        try {
            $cache_file->drop();
            $this->assertTrue(true);
        } catch (\Language\Exception\WrongParam $e) {
            $this->assertTrue(false);
        }
    }
}
