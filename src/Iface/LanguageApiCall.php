<?php
namespace Language\Iface;
use Language\Iface;

interface LanguageApiCall extends Iface\ApiCall
{
    /**
     * @param $applet
     * @throws LanguageException
     * @return array
     */
    public function getAppletLanguages($applet);

    /**
     * @param $language
     * @throws LanguageException
     * @return array
     */
    public function getLanguageFile($language);

    /**
     * @param $language
     * @param $applet
     * @throws LanguageException
     * @return array
     */
    public function getAppletLanguageFile($language, $applet);
}