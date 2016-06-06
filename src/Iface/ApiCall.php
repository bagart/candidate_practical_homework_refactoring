<?php
namespace Language\Iface;

interface ApiCall
{
    /**
     * @param bool $bool
     * @return $this
     */
    public function setAllowEmptyData($bool);

    /**
     * @param array $get
     * @param array $post
     * @return mixed
     */
    public function call(array $get = [], array $post = []);
}