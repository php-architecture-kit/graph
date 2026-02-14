<?php

declare(strict_types=1);

namespace PhpArchitecture\Graph\Vertex;

use PhpArchitecture\Graph\GraphTrait;
use PhpArchitecture\Graph\Vertex\Identity\VertexId;

class VertexStore
{
    use GraphTrait;

    /**
     * @var array<string,Vertex>
     */
    private array $store = [];

    public function addVertex(Vertex $vertex): void
    {
        if (isset($this->store[$vertex->id->toString()])) {
            throw new Exception\VertexAlreadyExistsException('Vertex `' . $vertex->id->toString() . '` already exists');
        }

        $this->store[$vertex->id->toString()] = $vertex;
        $this->graph()->indexNotifier->notifyVertexAdded($vertex);
    }

    public function count(): int
    {
        return count($this->store);
    }

    /**
     * @param ?callable(Vertex):bool $filter
     * @return array<string,Vertex>
     */
    public function getAllVertexes(?callable $filter = null): array
    {
        return $filter ? array_filter($this->store, $filter) : $this->store;
    }

    public function getVertex(VertexId $id, bool $throwException = false): ?Vertex
    {
        if ($throwException && !isset($this->store[$id->toString()])) {
            throw new Exception\VertexNotFoundException('Vertex `' . $id->toString() . '` not found');
        }

        return $this->store[$id->toString()] ?? null;
    }

    public function hasVertex(VertexId $id): bool
    {
        return isset($this->store[$id->toString()]);
    }

    public function removeVertex(VertexId $id): void
    {
        if (!isset($this->store[$id->toString()])) {
            throw new Exception\VertexNotFoundException('Vertex `' . $id->toString() . '` not found');
        }

        $vertex = $this->store[$id->toString()];

        $edgeStore = $this->graph()->edgeStore;
        foreach ($edgeStore->getAllEdges() as $edge) {
            if ($edge->u()->equals($id) || $edge->v()->equals($id)) {
                $edgeStore->removeEdge($edge->id);
            }
        }

        unset($this->store[$id->toString()]);
        $this->graph()->indexNotifier->notifyVertexRemoved($vertex);
    }
}
