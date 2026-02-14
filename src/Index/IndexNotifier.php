<?php

declare(strict_types=1);

namespace PhpArchitecture\Graph\Index;

use PhpArchitecture\Graph\Edge\Edge;
use PhpArchitecture\Graph\Events\EventDispatcher;
use PhpArchitecture\Graph\Vertex\Vertex;

class IndexNotifier
{
    private EventDispatcher $eventDispatcher;

    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function notifyVertexAdded(Vertex $vertex): void
    {
        $this->eventDispatcher->dispatchVertexAdded($vertex);
    }

    public function notifyVertexRemoved(Vertex $vertex): void
    {
        $this->eventDispatcher->dispatchVertexRemoved($vertex);
    }

    public function notifyEdgeAdded(Edge $edge): void
    {
        $this->eventDispatcher->dispatchEdgeAdded($edge);
    }

    public function notifyEdgeRemoved(Edge $edge): void
    {
        $this->eventDispatcher->dispatchEdgeRemoved($edge);
    }
}
