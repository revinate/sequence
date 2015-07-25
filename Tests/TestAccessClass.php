<?php
/**
 * Created by PhpStorm.
 * User: jasondent
 * Date: 24/07/2015
 * Time: 18:57
 */

namespace Revinate\SequenceBundle\Lib;


class TestAccessClass {
    protected $protected = 'protected';
    public $public = 'public';
    private $private = 'private';

    public function setPrivate($value) {
        $this->private = $value;
        return $this;
    }

    public function getPrivate() {
        return $this->private;
    }
}
