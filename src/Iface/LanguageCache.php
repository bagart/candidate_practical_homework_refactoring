<?php
namespace Language\Iface;

interface LanguageCache
{
    /**
     * LanguageCache constructor.
     * @throw LanguageException
     * @param string $language
     */
    public function __construct($language);

    /**
     * @param string $storage
     * @return $this
     */
    public function setStorage($storage);

    /**
     * @return string
     */
    public function getStorage();

    /**
     * @param $content
     * @throw LanguageException
     * @return $this
     */
    public function store($content);

    /**
     * @throw LanguageException
     * @return string
     */
    public function read();

    /**
     * @throw LanguageException
     * @return mixed
     */
    public function build();

    /**
     * @param string $data
     * @throw LanguageException
     * @return $this
     */
    public function checkContent($data);

    /**
     * @return string
     */
    public function getFileName();

    /**
     * @throw LanguageException
     * @return $this
     */
    public function drop();
}