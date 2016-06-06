<?php
namespace Language\Module\ApiCall;

final class Language extends AbsApiCall
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

    public function getAppletLanguageFile($applet, $language)
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