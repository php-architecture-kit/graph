<?php

declare(strict_types=1);

namespace PhpArchitecture\Graph\Index;

use PhpArchitecture\Graph\Edge\DirectedEdgeInterface;
use PhpArchitecture\Graph\Edge\UndirectedEdgeInterface;
use PhpArchitecture\Graph\Events\EventDispatcher;
use PhpArchitecture\Graph\Vertex\VertexInterface;

class IndexNotifier
{
    private EventDispatcher $eventDispatcher;

    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function notifyVertexAdded(VertexInterface $vertex): void
    {
        $this->eventDispatcher->dispatchVertexAdded($vertex);
    }

    public function notifyVertexRemoved(VertexInterface $vertex): void
    {
        $this->eventDispatcher->dispatchVertexRemoved($vertex);
    }

    public function notifyEdgeAdded(DirectedEdgeInterface|UndirectedEdgeInterface $edge): void
    {
        $this->eventDispatcher->dispatchEdgeAdded($edge);
    }

    public function notifyEdgeRemoved(DirectedEdgeInterface|UndirectedEdgeInterface $edge): void
    {
        $this->eventDispatcher->dispatchEdgeRemoved($edge);
    }
}
