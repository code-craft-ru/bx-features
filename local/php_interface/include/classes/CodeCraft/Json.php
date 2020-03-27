<?php
/**
 * JSON wrapper class
 *
 * @abstract
 *
 * @author    Roman Shershnev <readytoban@gmail.com>
 * @version   1.0
 * @package   CodeCraft
 * @category  Tools
 * @copyright Copyright © 2015, Roman Shershnev
 */

namespace CodeCraft;

abstract class Json {

    /* @var mixed $data */
    protected $data;

    /**
     * @param $request
     *
     * @return void
     */
    abstract protected function actionDefault($request);

    /**
     * @param $request
     *
     * @return $this
     */
    public function processRequest($request) {
        $action = 'action' . ucfirst($request['action']);
        if (isset($request['action']) && method_exists($this, $action)) {
            $this->$action($request);
        } else {
            $this->actionDefault($request);
        }

        return $this;
    }

    /**
     * @param mixed $data
     *
     * @return array
     */
    protected function statusOk($data = '') {
        return [
            'status' => 'ok',
            'data'   => $data
        ];
    }

    /**
     * @param mixed $data Error text
     *
     * @return array
     */
    protected function statusError($data = 'Unknown error.') {
        return [
            'status' => 'error',
            'data'   => $data
        ];
    }

    /**
     * @param bool/true $forceObject
     * 
     * @return string
     */
    public function getResponse($forceObject = true) {
        return json_encode($this->data, $forceObject ? JSON_FORCE_OBJECT : 0);
    }

}