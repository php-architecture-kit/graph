<?php

declare(strict_types=1);

namespace PhpArchitecture\Graph\Navigation\Traversal;

use PhpArchitecture\Graph\Edge\Identity\EdgeId;

final class EdgeTraversalResult
{
    /**
     * @var array<string,array<class-string<EdgeVisitorInterface>,VisitResult>>
     */
    private array $results = [];

    /**
     * @param class-string<EdgeVisitorInterface> $visitorClass
     */
    public function add(EdgeId $edgeId, string $visitorClass, VisitResult $result): void
    {
        $this->results[$edgeId->toString()][$visitorClass] = $result;
    }

    /**
     * @param class-string<EdgeVisitorInterface> $visitorClass
     */
    public function get(EdgeId $edgeId, string $visitorClass): ?VisitResult
    {
        return $this->results[$edgeId->toString()][$visitorClass] ?? null;
    }

    /**
     * @param class-string<EdgeVisitorInterface> $visitorClass
     */
    public function has(EdgeId $edgeId, string $visitorClass): bool
    {
        return isset($this->results[$edgeId->toString()][$visitorClass]);
    }

    /**
     * @return array<class-string<EdgeVisitorInterface>,VisitResult>
     */
    public function getByEdge(EdgeId $edgeId): array
    {
        return $this->results[$edgeId->toString()] ?? [];
    }

    /**
     * @param class-string<EdgeVisitorInterface> $visitorClass
     * @return array<string,VisitResult>
     */
    public function getByVisitor(string $visitorClass): array
    {
        $result = [];
        foreach ($this->results as $edgeId => $visitors) {
            if (isset($visitors[$visitorClass])) {
                $result[$edgeId] = $visitors[$visitorClass];
            }
        }

        return $result;
    }

    /**
     * @return array<string,array<class-string<EdgeVisitorInterface>,VisitResult>>
     */
    public function getAll(): array
    {
        return $this->results;
    }
}
