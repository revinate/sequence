<?php

namespace Revinate\Sequence;

class TraverseSequence extends RecursiveSequence {
    protected $path;

    /**
     * @param $iterator
     * @param null|string $path
     * @return TraverseSequence
     */
    public static function make($iterator, $path = null) {
        /** @var TraverseSequence $traverseSequence */
        $traverseSequence = parent::make($iterator);
        $traverseSequence->path = is_null($path) ? '' : $path . '.';
        return $traverseSequence;
    }

    /**
     * @return TraverseSequence|MappedSequence
     */
    public function getChildren() {
        $x = $this->current();
        if ($this->canGoDeeper()) {
            return TraverseSequence::make($x, $this->key())->setMaxDepth($this->depth - 1);
        } else {
            return IterationTraits::map(
                Sequence::make($x),
                FnGen::fnIdentity(),
                FnString::fnAddPrefix($this->key() . ".")
            );
        }
    }

    /**
     * @return string
     */
    public function key() {
        $key = parent::key();
        return $this->path . $key;
    }
}
