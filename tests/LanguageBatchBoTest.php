<?php
chdir(__DIR__ .'/..');

class LanguageBatchBoTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Language\LanguageBatchBo
     */
    private $app;
    protected $applications = ['portal'];
    protected $languages = ['en', 'hu'];

    protected  $filename_cache_php = [
        'portal' => [
            'en' => 'cache/portal/en.php',
            'hu' => 'cache/portal/hu.php',
        ]
    ];

    protected  $filename_cache_xml = [
        'en' => 'cache/flash/lang_en.xml',
        'hu' => 'cache/flash/lang_hu.xml',
    ];

    public function __construct($name, array $data, $dataName)
    {
        foreach ($this->filename_cache_xml as $key =>$path) {
            $this->filename_cache_xml[$key] = realpath($path);
        }
        
        foreach ($this->filename_cache_php as $key1 => $value) {
            foreach ($value as $key2 => $path) {
                $this->filename_cache_php[$key1][$key2] = realpath($path);
            }
        }
    }

    protected function getCacheXmlFiles()
    {
        return array_values($this->filename_cache_xml);
    }

    protected  function getCachePhpFiles()
    {
        $cache_list = [];
        foreach ($this->filename_cache_php as $application) {
            $cache_list = array_merge($cache_list, $application);
        }

        return $cache_list;
    }

    public function getApp()
    {
        if (!$this->app) {
            $this->app = new \Language\LanguageBatchBo();
            $this->app->setLogger(
                (new ExceptionLogger())
            );
        }
        
        return $this->app;
    }

    public function test_getLanguageCacheAppFileName()
    {
        foreach ($this->filename_cache_php as $application => $list1) {
            foreach ($list1 as $language => $file_mock) {
                $file = $this->getApp()->getLanguageCacheAppFileName($application, $language);

                $this->assertEquals(
                    realpath($file),
                    realpath($file_mock)
                );
            }
        }
    }


    public function test_getLanguageCacheXmlFileName()
    {
        foreach ($this->filename_cache_xml as $language => $file_mock) {
            $file = $this->getApp()->getLanguageCacheXmlFileName($language);

            $this->assertEquals(
                realpath($file),
                realpath($file_mock)
            );
        }
    }
    
    public function internal_cache_files_php()
    {
        $file_list = $this->getCacheXmlFiles();
        $this->assertNotEmpty($file_list);

        $this->assertEquals(
            $file_list,
            array_unique(array_filter($file_list))
        );
        
        foreach ($file_list as $file) {
            $this->assertTrue(file_exists($file));
            $this->assertTrue(filesize($file) > 10);

            $result = include $file;
            $this->assertArrayHasKey('favorites', $result);
            $this->assertArrayHasKey('boy', $result);
            $this->assertArrayHasKey('help', $result);

            $this->assertNotEmpty($result['favorites']);
            $this->assertNotEmpty($result['boy']);
            $this->assertNotEmpty($result['help']);
        }
    }

    public function internal_cache_files_xml()
    {
        $file_list = $this->getCacheXmlFiles();
        $this->assertNotEmpty($file_list);

        $this->assertEquals(
            $file_list,
            array_unique(array_filter($file_list))
        );
        
        foreach ($file_list as $file) {
            $this->assertTrue(file_exists($file));
            $this->assertTrue(filesize($file) > 10);

            $content = file_get_contents($file);
            $this->assertNotEmpty($content);
            $xml = simplexml_load_string($content);
            $this->assertNotEmpty($xml->count());
        }
    }

    public function test_generateLanguageFiles()
    {
        $start_time = time();
        $this->getApp()->generateLanguageFiles();

        foreach ($this->getCachePhpFiles() as $file) {
            $this->assertGreaterThanOrEqual(
                filectime($file),
                $start_time
            );
            
            $this->assertLessThanOrEqual(
                filectime($file),
                time()
            );
        }

        $this->internal_cache_files_php();
    }

    public function test_generateAppletLanguageXmlFiles()
    {
        $start_time = time();
        $this->getApp()->generateAppletLanguageXmlFiles();

        foreach ($this->getCachePhpFiles() as $file) {
            $this->assertGreaterThanOrEqual(
                filectime($file),
                $start_time
            );
            $this->assertLessThanOrEqual(
                filectime($file),
                time()
            );
        }

        $this->internal_cache_files_xml();
    }
    
    public function testLogger()
    {
        $this->assertTrue(
            $this->getApp()->getLogger() instanceof ExceptionLogger
        );
        
        try {
            $this->getApp()->getLogger()->info('all ok');
            $this->assertTrue(true);
        } catch (\Exception $e) {
            $this->assertTrue(false);
        }
        try {
            $this->getApp()->getLogger()->warning('all ok');
            $this->assertTrue(false);
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }
        
    }
}

class ExceptionLogger extends \Psr\Log\AbstractLogger
{
    private $level = \Monolog\Logger::WARNING;

    public function log($level, $message, array $context = array())
    {
        if (\Monolog\Logger::toMonologLevel($level) >= $this->level) {
            throw new \Exception($message);
        }
    }
}
