<?php

declare(strict_types=1);

namespace PhpArchitecture\Graph\Tools;

use PhpArchitecture\Graph\Edge\DirectedEdgeInterface;
use PhpArchitecture\Graph\Edge\UndirectedEdgeInterface;
use PhpArchitecture\Graph\Graph;
use PhpArchitecture\Graph\Vertex\Identity\VertexId;

final class BidirectionalShortestPathFinder
{
    public function __construct(
        private readonly Graph $graph,
    ) {}

    /**
     * @param callable(DirectedEdgeInterface|UndirectedEdgeInterface):bool $edgeFilter
     * @return list<DirectedEdgeInterface|UndirectedEdgeInterface>
     */
    public function find(VertexId $sourceId, VertexId $targetId, ?callable $edgeFilter = null): array
    {
        if ($sourceId->equals($targetId)) {
            return [];
        }

        $adjacency = $this->buildAdjacencyMap($edgeFilter);

        $currentFrontLine = [$sourceId->toString() => $sourceId];
        $targetFrontLine = [$targetId->toString() => $targetId];

        $currentVisited = $currentFrontLine;
        $targetVisited = $targetFrontLine;

        $currentParents = [];
        $targetParents = [];

        $currentExpanded = false;
        $targetExpanded = false;

        $meetingVertexId = null;

        while ($currentFrontLine !== [] && $targetFrontLine !== []) {
            $meetingVertexId = $this->findCommonVertexId($currentFrontLine, $targetVisited);
            if ($meetingVertexId !== null) {
                break;
            }

            $expandCurrent = $this->shouldExpandCurrentFrontLine(
                $currentExpanded,
                $targetExpanded,
                $currentFrontLine,
                $targetFrontLine,
                $adjacency,
            );

            if ($expandCurrent) {
                [$currentFrontLine, $meetingVertexId] = $this->expandFrontLine(
                    $currentFrontLine,
                    $currentVisited,
                    $currentParents,
                    $targetVisited,
                    $adjacency,
                );
                $currentExpanded = true;
            } else {
                [$targetFrontLine, $meetingVertexId] = $this->expandFrontLine(
                    $targetFrontLine,
                    $targetVisited,
                    $targetParents,
                    $currentVisited,
                    $adjacency,
                );
                $targetExpanded = true;
            }

            if ($meetingVertexId !== null) {
                break;
            }
        }

        if ($meetingVertexId === null) {
            return [];
        }

        return $this->buildPath(
            $sourceId,
            $meetingVertexId,
            $targetId,
            $currentParents,
            $targetParents,
        );
    }

    /**
     * @param callable(DirectedEdgeInterface|UndirectedEdgeInterface):bool $edgeFilter
     * @return array<string,list<array{neighbor:VertexId,edge:DirectedEdgeInterface|UndirectedEdgeInterface}>>
     */
    private function buildAdjacencyMap(?callable $edgeFilter): array
    {
        $adjacency = [];
        foreach ($this->graph->edgeStore->getEdges($edgeFilter) as $edge) {
            $u = $edge->u();
            $v = $edge->v();

            $adjacency[$u->toString()][] = ['neighbor' => $v, 'edge' => $edge];
            $adjacency[$v->toString()][] = ['neighbor' => $u, 'edge' => $edge];
        }

        return $adjacency;
    }

    /**
     * @param array<string,VertexId> $frontLine
     * @param array<string,VertexId> $otherVisited
     */
    private function findCommonVertexId(array $frontLine, array $otherVisited): ?VertexId
    {
        foreach ($frontLine as $vertexIdString => $vertexId) {
            if (isset($otherVisited[$vertexIdString])) {
                return $vertexId;
            }
        }

        return null;
    }

    /**
     * @param array<string,VertexId> $currentFrontLine
     * @param array<string,VertexId> $targetFrontLine
     * @param array<string,list<array{neighbor:VertexId,edge:DirectedEdgeInterface|UndirectedEdgeInterface}>> $adjacency
     */
    private function shouldExpandCurrentFrontLine(
        bool $currentExpanded,
        bool $targetExpanded,
        array $currentFrontLine,
        array $targetFrontLine,
        array $adjacency,
    ): bool {
        if (!$currentExpanded) {
            return true;
        }

        if (!$targetExpanded) {
            return false;
        }

        return $this->frontLineEdgeCount($currentFrontLine, $adjacency)
            <= $this->frontLineEdgeCount($targetFrontLine, $adjacency);
    }

    /**
     * @param array<string,VertexId> $frontLine
     * @param array<string,list<array{neighbor:VertexId,edge:DirectedEdgeInterface|UndirectedEdgeInterface}>> $adjacency
     */
    private function frontLineEdgeCount(array $frontLine, array $adjacency): int
    {
        $count = 0;
        foreach ($frontLine as $vertexIdString => $_vertexId) {
            $count += count($adjacency[$vertexIdString] ?? []);
        }

        return $count;
    }

    /**
     * @param array<string,VertexId> $frontLine
     * @param array<string,VertexId> $visited
     * @param array<string,array{vertex:VertexId,edge:DirectedEdgeInterface|UndirectedEdgeInterface}> $parents
     * @param array<string,VertexId> $otherVisited
     * @param array<string,list<array{neighbor:VertexId,edge:DirectedEdgeInterface|UndirectedEdgeInterface}>> $adjacency
     * @return array{0:array<string,VertexId>,1:?VertexId}
     */
    private function expandFrontLine(
        array $frontLine,
        array &$visited,
        array &$parents,
        array $otherVisited,
        array $adjacency,
    ): array {
        $nextFrontLine = [];

        foreach ($frontLine as $vertexIdString => $vertexId) {
            foreach ($adjacency[$vertexIdString] ?? [] as $connection) {
                $neighbor = $connection['neighbor'];
                $neighborIdString = $neighbor->toString();

                if (isset($visited[$neighborIdString])) {
                    continue;
                }

                $visited[$neighborIdString] = $neighbor;
                $parents[$neighborIdString] = [
                    'vertex' => $vertexId,
                    'edge' => $connection['edge'],
                ];
                $nextFrontLine[$neighborIdString] = $neighbor;

                if (isset($otherVisited[$neighborIdString])) {
                    return [$nextFrontLine, $neighbor];
                }
            }
        }

        return [$nextFrontLine, null];
    }

    /**
     * @param array<string,array{vertex:VertexId,edge:DirectedEdgeInterface|UndirectedEdgeInterface}> $currentParents
     * @param array<string,array{vertex:VertexId,edge:DirectedEdgeInterface|UndirectedEdgeInterface}> $targetParents
     * @return list<DirectedEdgeInterface|UndirectedEdgeInterface>
     */
    private function buildPath(
        VertexId $sourceId,
        VertexId $meetingVertexId,
        VertexId $targetId,
        array $currentParents,
        array $targetParents,
    ): array {
        $fromSource = [];
        $cursor = $meetingVertexId;
        while (!$cursor->equals($sourceId)) {
            $cursorIdString = $cursor->toString();
            if (!isset($currentParents[$cursorIdString])) {
                return [];
            }

            $step = $currentParents[$cursorIdString];
            $fromSource[] = $step['edge'];
            $cursor = $step['vertex'];
        }

        $toTarget = [];
        $cursor = $meetingVertexId;
        while (!$cursor->equals($targetId)) {
            $cursorIdString = $cursor->toString();
            if (!isset($targetParents[$cursorIdString])) {
                return [];
            }

            $step = $targetParents[$cursorIdString];
            $toTarget[] = $step['edge'];
            $cursor = $step['vertex'];
        }

        return array_merge(array_reverse($fromSource), $toTarget);
    }
}
