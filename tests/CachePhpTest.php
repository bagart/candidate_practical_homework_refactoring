<?php

class CachePhpTest extends PHPUnit_Framework_TestCase
{
    protected $mock_content = '<?php return ["foo"=> "bar"];';
    
    protected  $filename_cache_php = [
        'portal' => [
            'en' => 'cache/portal/en.php',
            'hu' => 'cache/portal/hu.php',
        ]
    ];

    public function setUp()
    {
        foreach ($this->filename_cache_php as $key1 => $value) {
            foreach ($value as $key2 => $path) {
                $this->filename_cache_php[$key1][$key2] = realpath($path);
            }
        }
    }

    public function test_create()
    {
        try {
            new \Language\Module\Cache\Php('test_lang', 'test_app');
            $this->assertTrue(true);
        } catch (\Language\Exception\WrongParam $e) {
            $this->assertTrue(false);
        }

        try {
            new \Language\Module\Cache\Php('test_lang');
            $this->assertTrue(false, 'undef second param');
        } catch (\Language\Exception\WrongParam $e) {
            $this->assertTrue(true);
        }
        try {
            new \Language\Module\Cache\Php(null, 'test_app');
            $this->assertTrue(false);
        } catch (\Language\Exception\WrongParam $e) {
            $this->assertTrue(true);
        }

        try {
            new \Language\Module\Cache\Php('foo/bar', 'test_app');
            $this->assertTrue(false);
        } catch (\Language\Exception\WrongParam $e) {
            $this->assertTrue(true);
        }

        try {
            new \Language\Module\Cache\Php('test_lang', 'foo/bar');
            $this->assertTrue(false);
        } catch (\Language\Exception\WrongParam $e) {
            $this->assertTrue(true);
        }
    }

    public function test_store()
    {
        $test_path = __DIR__ . '/../cache/test_app/test_lang.php';
        if (file_exists($test_path)) {
            unlink($test_path);
        }
        $this->assertFalse(file_exists($test_path));

        $cache_file = (new \Language\Module\Cache\Php('test_lang', 'test_app'))
            ->store($this->mock_content);
        $this->assertEquals(
            realpath($cache_file->getFileName()),
            realpath($test_path)
        );
        $this->internal_testFile($cache_file->getFileName());
        $this->assertEquals(
            file_get_contents($cache_file->getFileName()),
            $cache_file->read()
        );
        try {
            $result = $cache_file->build();
            $this->assertTrue(count($result) == 1);
            $this->assertTrue(!empty($result['foo']));
            $this->assertTrue($result['foo'] === 'bar');
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
    
    public function getCachePhpFiles()
    {
        $result = [];
        foreach ($this->filename_cache_php as $values) {
            $result = array_merge($result, $values);
        }
        
        return $result;
    }

    public function internal_testFile($filename)
    {
        $this->assertTrue(file_exists($filename));
        $this->assertTrue(filesize($filename) > 10);
        
        $result = include($filename);
        $this->assertNotEmpty($result);
        $this->assertTrue(is_array($result));
        $this->assertTrue(count($result) > 0);
    }

    public function test_real_files()
    {
        $file_list = $this->getCachePhpFiles();
        $this->assertNotEmpty($file_list);

        $this->assertEquals(
            $file_list,
            array_unique(array_filter($file_list))
        );

        foreach ($file_list as $filename) {
            $this->internal_testFile($filename);

            $result = include $filename;
            $this->assertTrue(is_array($result));
            $this->assertFalse(empty($result['favorites']));
            $this->assertFalse(empty($result['boy']));
            $this->assertFalse(empty($result['help']));
        }
    }
}
