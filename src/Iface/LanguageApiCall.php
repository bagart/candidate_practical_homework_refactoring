<?php
namespace Language\Iface;

interface LanguageApiCall extends ApiCall
{
    /**
     * @param $applet
     * @return array
     */
    public function getAppletLanguages($applet);

    /**
     * @param $language
     * @return array
     */
    public function getLanguageFile($language);

    /**
     * @param $language
     * @param $applet
     * @return array
     */
    public function getAppletLanguageFile($language, $applet);
}