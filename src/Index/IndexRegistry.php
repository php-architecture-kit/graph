<?php

declare(strict_types=1);

namespace PhpArchitecture\Graph\Index;

use PhpArchitecture\Graph\Edge\Edge;
use PhpArchitecture\Graph\Events\Listener\OnEdgeAddedInterface;
use PhpArchitecture\Graph\Events\Listener\OnEdgeRemovedInterface;
use PhpArchitecture\Graph\Events\Listener\OnVertexAddedInterface;
use PhpArchitecture\Graph\Events\Listener\OnVertexRemovedInterface;
use PhpArchitecture\Graph\Vertex\Vertex;

class IndexRegistry implements OnVertexAddedInterface, OnVertexRemovedInterface, OnEdgeAddedInterface, OnEdgeRemovedInterface
{
    /** @var array<class-string,object> */
    private array $indexes = [];

    public function register(object $index): void
    {
        $this->indexes[$index::class] = $index;
    }

    public function unregister(object $index): void
    {
        unset($this->indexes[$index::class]);
    }

    /**
     * @template T of object
     * @param class-string<T> $class
     * @return T|null
     */
    public function index(string $class): ?object
    {
        if (!isset($this->indexes[$class])) {
            return null;
        }

        /** @var T $index */
        $index = $this->indexes[$class];

        return $index;
    }

    /**
     * @return array<class-string,object>
     */
    public function all(): array
    {
        return $this->indexes;
    }

    public function onVertexAdded(Vertex $vertex): void
    {
        foreach ($this->indexes as $index) {
            if ($index instanceof OnVertexAddedInterface) {
                $index->onVertexAdded($vertex);
            }
        }
    }

    public function onVertexRemoved(Vertex $vertex): void
    {
        foreach ($this->indexes as $index) {
            if ($index instanceof OnVertexRemovedInterface) {
                $index->onVertexRemoved($vertex);
            }
        }
    }

    public function onEdgeAdded(Edge $edge): void
    {
        foreach ($this->indexes as $index) {
            if ($index instanceof OnEdgeAddedInterface) {
                $index->onEdgeAdded($edge);
            }
        }
    }

    public function onEdgeRemoved(Edge $edge): void
    {
        foreach ($this->indexes as $index) {
            if ($index instanceof OnEdgeRemovedInterface) {
                $index->onEdgeRemoved($edge);
            }
        }
    }
}
