<?php

declare(strict_types=1);

namespace PhpArchitecture\Graph\Vertex;

use PhpArchitecture\Graph\Events\EventDispatcher;
use PhpArchitecture\Graph\Utils\GraphTrait;
use PhpArchitecture\Graph\Vertex\Identity\VertexId;

class VertexStore
{
    use GraphTrait;

    /**
     * @param array<string,VertexInterface> $store
     */
    public function __construct(
        private array $store,
        private readonly EventDispatcher $eventDispatcher,
    ) {}

    public function addVertex(VertexInterface $vertex): void
    {
        if (isset($this->store[$vertex->id()->toString()])) {
            throw new Exception\VertexAlreadyExistsException('Vertex `' . $vertex->id()->toString() . '` already exists');
        }

        $this->store[$vertex->id()->toString()] = $vertex;
        $this->eventDispatcher->dispatchVertexAdded($vertex);
    }

    public function count(): int
    {
        return count($this->store);
    }

    /**
     * @param ?callable(VertexInterface):bool $filter
     * @return array<string,VertexInterface>
     */
    public function getVertices(?callable $filter = null): array
    {
        return $filter ? array_filter($this->store, $filter) : $this->store;
    }

    public function getVertex(VertexId $id, bool $throwException = false): ?VertexInterface
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
        foreach ($edgeStore->getEdges() as $edge) {
            if ($edge->u()->equals($id) || $edge->v()->equals($id)) {
                $edgeStore->removeEdge($edge->id());
            }
        }

        unset($this->store[$id->toString()]);
        $this->eventDispatcher->dispatchVertexRemoved($vertex);
    }
}
