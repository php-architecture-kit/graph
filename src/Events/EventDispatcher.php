<?php

declare(strict_types=1);

namespace PhpArchitecture\Graph\Events;

use PhpArchitecture\Graph\Edge\DirectedEdgeInterface;
use PhpArchitecture\Graph\Edge\UndirectedEdgeInterface;
use PhpArchitecture\Graph\Events\Listener\OnEdgeAddedInterface;
use PhpArchitecture\Graph\Events\Listener\OnEdgeRemovedInterface;
use PhpArchitecture\Graph\Events\Listener\OnVertexAddedInterface;
use PhpArchitecture\Graph\Events\Listener\OnVertexRemovedInterface;
use PhpArchitecture\Graph\Vertex\VertexInterface;

class EventDispatcher
{
    /** @var array<int,OnVertexAddedInterface> */
    private array $onVertexAddedListeners = [];

    /** @var array<int,OnVertexRemovedInterface> */
    private array $onVertexRemovedListeners = [];

    /** @var array<int,OnEdgeAddedInterface> */
    private array $onEdgeAddedListeners = [];

    /** @var array<int,OnEdgeRemovedInterface> */
    private array $onEdgeRemovedListeners = [];

    public function addOnVertexAddedListener(OnVertexAddedInterface $listener): void
    {
        $this->onVertexAddedListeners[spl_object_id($listener)] = $listener;
    }

    public function removeOnVertexAddedListener(OnVertexAddedInterface $listener): void
    {
        unset($this->onVertexAddedListeners[spl_object_id($listener)]);
    }

    public function addOnVertexRemovedListener(OnVertexRemovedInterface $listener): void
    {
        $this->onVertexRemovedListeners[spl_object_id($listener)] = $listener;
    }

    public function removeOnVertexRemovedListener(OnVertexRemovedInterface $listener): void
    {
        unset($this->onVertexRemovedListeners[spl_object_id($listener)]);
    }

    public function addOnEdgeAddedListener(OnEdgeAddedInterface $listener): void
    {
        $this->onEdgeAddedListeners[spl_object_id($listener)] = $listener;
    }

    public function removeOnEdgeAddedListener(OnEdgeAddedInterface $listener): void
    {
        unset($this->onEdgeAddedListeners[spl_object_id($listener)]);
    }

    public function addOnEdgeRemovedListener(OnEdgeRemovedInterface $listener): void
    {
        $this->onEdgeRemovedListeners[spl_object_id($listener)] = $listener;
    }

    public function removeOnEdgeRemovedListener(OnEdgeRemovedInterface $listener): void
    {
        unset($this->onEdgeRemovedListeners[spl_object_id($listener)]);
    }

    public function dispatchVertexAdded(VertexInterface $vertex): void
    {
        foreach ($this->onVertexAddedListeners as $listener) {
            $listener->onVertexAdded($vertex);
        }
    }

    public function dispatchVertexRemoved(VertexInterface $vertex): void
    {
        foreach ($this->onVertexRemovedListeners as $listener) {
            $listener->onVertexRemoved($vertex);
        }
    }

    public function dispatchEdgeAdded(DirectedEdgeInterface|UndirectedEdgeInterface $edge): void
    {
        foreach ($this->onEdgeAddedListeners as $listener) {
            $listener->onEdgeAdded($edge);
        }
    }

    public function dispatchEdgeRemoved(DirectedEdgeInterface|UndirectedEdgeInterface $edge): void
    {
        foreach ($this->onEdgeRemovedListeners as $listener) {
            $listener->onEdgeRemoved($edge);
        }
    }
}
