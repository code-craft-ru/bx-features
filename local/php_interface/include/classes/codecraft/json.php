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
    var $data;

    /**
     * @param $request
     *
     * @return $this
     */
    abstract protected function actionDefault($request);

    /**
     * @param $request
     *
     * @return $this
     */
    public function processRequest($request) {
        switch ($request['action']) {
            case 'willgo':
                $this->actionWillGo($request['id']);
                break;
            default:

                break;
        }
        if (isset($request['action']) && is_callable([$this, 'action'.ucfirst($request['action'])])) {
            call_user_func([$this, 'action'.ucfirst($request['action'])], $request);
        } else {
            $this->actionDefault($request);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getResponse() {
        return json_encode($this->data, JSON_FORCE_OBJECT);
    }

}