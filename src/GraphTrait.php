<?php

declare(strict_types=1);

namespace PhpArchitecture\Graph;

use WeakReference;

trait GraphTrait
{
    /** @var \WeakReference<Graph>|null */
    protected ?\WeakReference $graph = null;

    protected function graph(): Graph
    {
        if ($this->graph === null) {
            throw new Exception\GraphNotSetException('Graph has not been set.');
        }

        $graph = $this->graph->get();
        if ($graph === null) {
            throw new Exception\GraphGarbageCollectedException('Graph is no longer available.');
        }

        return $graph;
    }

    public function setGraph(Graph $graph): void
    {
        $this->graph = \WeakReference::create($graph);
    }

    public function unsetGraph(): void
    {
        $this->graph = null;
    }
}
