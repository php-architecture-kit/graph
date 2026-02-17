<?php

declare(strict_types=1);

namespace PhpArchitecture\Graph;

use PhpArchitecture\Graph\Edge\DirectedEdgeInterface;
use PhpArchitecture\Graph\Edge\EdgeContext;
use PhpArchitecture\Graph\Edge\Identity\EdgeId;
use PhpArchitecture\Graph\Edge\UndirectedEdgeInterface;
use PhpArchitecture\Graph\Navigation\Traversal;
use PhpArchitecture\Graph\Tools\BidirectionalShortestPathFinder;
use PhpArchitecture\Graph\Vertex\Identity\VertexId;
use PhpArchitecture\Graph\Vertex\VertexInterface;
use PhpArchitecture\Graph\Vertex\VertexContext;

class GraphNavigator
{
    public function __construct(
        public readonly Graph $graph
    ) {}

    public function selectEdge(EdgeId $id): EdgeContext
    {
        /** @var DirectedEdgeInterface|UndirectedEdgeInterface $edge */
        $edge = $this->graph->edgeStore->getEdge($id, true);

        return new EdgeContext($this->graph, $edge);
    }

    /**
     * @param callable(DirectedEdgeInterface|UndirectedEdgeInterface):bool $filter
     *
     * @return EdgeContext[]
     */
    public function selectEdges(?callable $filter = null): array
    {
        $edges = $this->graph->edgeStore->getEdges($filter);

        return array_map(
            fn(DirectedEdgeInterface|UndirectedEdgeInterface $edge): EdgeContext => new EdgeContext($this->graph, $edge),
            $edges,
        );
    }

    public function selectVertex(VertexId $id): VertexContext
    {
        /** @var VertexInterface $vertex */
        $vertex = $this->graph->vertexStore->getVertex($id, true);

        return new VertexContext($this->graph, $vertex);
    }

    /**
     * @param callable(VertexInterface):bool $filter
     *
     * @return VertexContext[]
     */
    public function selectVertices(?callable $filter = null): array
    {
        $vertices = $this->graph->vertexStore->getVertices($filter);

        return array_map(
            fn(VertexInterface $vertex): VertexContext => new VertexContext($this->graph, $vertex),
            $vertices,
        );
    }

    /**
     * @param callable(DirectedEdgeInterface|UndirectedEdgeInterface):bool $edgeFilter
     *
     * @return EdgeContext[]
     */
    public function shortestPathTo(VertexId $sourceId, VertexId $targetId, ?callable $edgeFilter = null): array
    {
        $finder = new BidirectionalShortestPathFinder($this->graph);
        $pathEdges = $finder->find($sourceId, $targetId, $edgeFilter);

        return array_map(
            fn(DirectedEdgeInterface|UndirectedEdgeInterface $edge): EdgeContext => new EdgeContext($this->graph, $edge),
            $pathEdges,
        );
    }

    /**
     * @param Traversal\VertexVisitorInterface[] $visitors
     * @param callable(VertexInterface):bool $filter
     */
    public function traverseVertices(array $visitors, ?callable $filter = null): Traversal\VertexTraversalResult
    {
        $traversal = new Traversal\VertexTraversal($visitors);

        return $traversal->traverse($this->graph, $filter);
    }

    /**
     * @param Traversal\EdgeVisitorInterface[] $visitors
     * @param callable(DirectedEdgeInterface|UndirectedEdgeInterface):bool $filter
     */
    public function traverseEdges(array $visitors, ?callable $filter = null): Traversal\EdgeTraversalResult
    {
        $traversal = new Traversal\EdgeTraversal($visitors);

        return $traversal->traverse($this->graph, $filter);
    }
}
