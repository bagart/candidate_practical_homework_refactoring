<?php

class ApiCallLanguageTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Language\Module\ApiCall\Language
     */
    private $app;
    protected $applet = 'JSM2_MemberApplet';
    protected $language = 'en';

    public function getApp()
    {
        if (!$this->app) {
            $this->app = new \Language\Module\ApiCall\Language();
        }

        return $this->app;
    }

    public function test_getAppletLanguages()
    {
        $result = $this->getApp()->getAppletLanguages($this->applet);
        $this->assertNotEmpty($result);
        $this->assertTrue(!empty($result[0]));
        $this->assertTrue(in_array('en', $result));
    }
    public function test_getLanguageFile()
    {
        $result = $this->getApp()->getLanguageFile($this->language);
        $this->assertNotEmpty($result);
        $this->assertTrue(strpos($result, '<?php') === 0);
    }
    
    public function test_getAppletLanguageFile()
    {
        $result = $this->getApp()->getAppletLanguageFile($this->language, $this->applet);
        $this->assertNotEmpty($result);
        $xml = simplexml_load_string($result);
        $this->assertNotEmpty($xml->asXML());
        $this->assertNotEmpty($xml->count());
    }
}
