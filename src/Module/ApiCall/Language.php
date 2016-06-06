<?php
namespace Language\Module\ApiCall;

use Language\Iface;

final class Language extends AbsApiCall implements Iface\LanguageApiCall
{
    protected $get = [
        'system' => 'LanguageFiles',
    ];

    public function getAppletLanguages($applet)
    {
        return $this->call(
            [
                'action' => 'getAppletLanguages'
            ],
            [
                'applet' => $applet
            ]
        );
    }

    public function getLanguageFile($language)
    {
        return $this->call(
            [
                'action' => 'getLanguageFile'
            ],
            [
                'language' => $language
            ]
        );
    }

    public function getAppletLanguageFile($language, $applet)
    {
        return $this->call(
            [
                'action' => 'getAppletLanguageFile'
            ],
            [
                'applet' => $applet,
				'language' => $language
            ]
        );
    }
}