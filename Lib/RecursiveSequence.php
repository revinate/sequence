<?php
namespace Revinate\SequenceBundle\Lib;

use \RecursiveIterator;

class RecursiveSequence extends Sequence implements RecursiveIterator  {
    protected $depth = -1;

    public function canGoDeeper() {
        return ($this->depth - 1) !== 0;
    }

    /**
     * @return RecursiveSequence
     */
    public function getChildren() {
        $x = $this->current();
        if ($this->canGoDeeper()) {
            return RecursiveSequence::make($x);
        } else {
            return Sequence::make($x);
        }
    }

    /**
     * @param $depth
     * @return $this
     */
    public function setMaxDepth($depth = -1) {
        $this->depth = $depth;
        return $this;
    }

    /**
     * @return bool - true if we can make a sequence out of the current item.
     */
    public function hasChildren() {
        return $this->valid() && $this->canBeSequence($this->current());
    }
}