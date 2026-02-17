<?php

declare(strict_types=1);

namespace PhpArchitecture\Graph\Navigation\Traversal;

use PhpArchitecture\Graph\Vertex\Identity\VertexId;

final class VertexTraversalResult
{
    /**
     * @var array<string,array<class-string<VertexVisitorInterface>,VisitResult>>
     */
    private array $results = [];

    /**
     * @param class-string<VertexVisitorInterface> $visitorClass
     */
    public function add(VertexId $vertexId, string $visitorClass, VisitResult $result): void
    {
        $this->results[$vertexId->toString()][$visitorClass] = $result;
    }

    /**
     * @param class-string<VertexVisitorInterface> $visitorClass
     */
    public function get(VertexId $vertexId, string $visitorClass): ?VisitResult
    {
        return $this->results[$vertexId->toString()][$visitorClass] ?? null;
    }

    /**
     * @param class-string<VertexVisitorInterface> $visitorClass
     */
    public function has(VertexId $vertexId, string $visitorClass): bool
    {
        return isset($this->results[$vertexId->toString()][$visitorClass]);
    }

    /**
     * @return array<class-string<VertexVisitorInterface>,VisitResult>
     */
    public function getByVertex(VertexId $vertexId): array
    {
        return $this->results[$vertexId->toString()] ?? [];
    }

    /**
     * @param class-string<VertexVisitorInterface> $visitorClass
     * @return array<string,VisitResult>
     */
    public function getByVisitor(string $visitorClass): array
    {
        $result = [];
        foreach ($this->results as $vertexId => $visitors) {
            if (isset($visitors[$visitorClass])) {
                $result[$vertexId] = $visitors[$visitorClass];
            }
        }

        return $result;
    }

    /**
     * @return array<string,array<class-string<VertexVisitorInterface>,VisitResult>>
     */
    public function getAll(): array
    {
        return $this->results;
    }
}
