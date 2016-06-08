<?php
namespace Language\Module\ApiCall;
use Language\Exception as E;
use Language\ApiCall;
use Language\Iface;


abstract class AbsApiCall implements Iface\ApiCall
{
    protected $target = 'system_api';
    protected $mode = 'language_api';
    protected $get = [];
    protected $post = [];

    protected $allow_empty_data = true;

    public function setAllowEmptyData($bool)
    {
        $this->allow_empty_data = (bool) $bool;
        
        return $this;
    }

    public function call(array $get = [], array $post = [])
    {
        $get = $get + $this->get;
        $post = $post + $this->post;
        $result = ApiCall::call(
            $this->target,
            $this->mode,
            $get,
            $post
        );

        $this->checkForApiErrorResult($result, $get, $post);

        return $result['data'];
    }

    /**
     * Checks the api call result.
     *
     * @param array $result   The api call result to check.
     * @param array $get;
     * @param array $post;
     * @throws E\ErrorResult If the api call was not successful.
     *
     * @return true
     */
    protected function checkForApiErrorResult($result, array $get = [], array $post = [])
    {
        $get += ['action' => 'unknown'];
        $param = array_filter([
            'get' => array_diff_key($get, ['action' => true ] + $this->get),
            'post' => array_diff_key($get, ['action' => true ] + $this->get),
        ]);
        $message = 'Error with ' . __CLASS__ . " action: {$get['action']}"
            . ($param ? ', param: ' . var_export($post, true) : null) 
            . "\nDetail: ";

        if (!$result) {
            throw new E\ErrorResult("{$message}Empty result");
        }

        if (empty($result['status'])) {
            throw new E\ErrorResult("{$message}Empty status");
        }
        // Wrong response.
        if ($result['status'] != 'OK') {
            throw new E\ErrorResult(
                "{$message}Wrong response: "
                . (!empty($result['error_type']) ? 'Type(' . $result['error_type'] . ') ' : '')
                . (!empty($result['error_code']) ? 'Code(' . $result['error_code'] . ') ' : '')
                . ((string)$result['data'])
            );
        }

        if (!isset($result['data']) || $result['data'] === false) {
            throw new E\ErrorResult("{$message}Wrong content with valid status!");
        }

        if (!$this->allow_empty_data && !$result['data']) {
            throw new E\ErrorResult("{$message}Empty data disabled");
        }        

        return $this;
    }

}