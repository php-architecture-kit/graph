<?php

declare(strict_types=1);

namespace PhpArchitecture\Graph\Vertex;

use PhpArchitecture\Graph\Edge\EdgeInterface;
use PhpArchitecture\Graph\Edge\EdgeContext;
use PhpArchitecture\Graph\Graph;
use PhpArchitecture\Graph\Vertex\VertexInterface;

class VertexContext
{
    public function __construct(
        public readonly Graph $graph,
        public readonly VertexInterface $vertex,
    ) {}

    /**
     * @param callable(EdgeInterface):bool $filter
     * 
     * @return EdgeContext[]
     */
    public function edges(?callable $filter = null): array
    {
        $edges = $this->graph->edgeStore->getEdges(
            filter: function (EdgeInterface $edge) use ($filter): bool {
                $isRelated = $this->vertex->id()->equals($edge->v()) || $this->vertex->id()->equals($edge->u());

                return $isRelated && (!$filter || $filter($edge));
            },
        );

        $contexts = [];
        foreach ($edges as $edge) {
            $contexts[] = new EdgeContext($this->graph, $edge);
        }

        return $contexts;
    }

    /**
     * @param callable(EdgeInterface):bool $edgeFilter
     * @param callable(EdgeInterface,VertexInterface):bool $filter
     * 
     * @return self[]
     */
    public function neighbors(?callable $edgeFilter = null, ?callable $filter = null): array
    {
        $edges = $this->edges($edgeFilter);

        $neighbors = [];
        foreach ($edges as $edgeContext) {
            $edge = $edgeContext->edge;
            $neighborId = $edge->v()->equals($this->vertex->id()) ? $edge->u() : $edge->v();
            /** @var VertexInterface $neighbor */
            $neighbor = $this->graph->vertexStore->getVertex($neighborId, true);

            if ($filter && !$filter($edge, $neighbor)) {
                continue;
            }

            $neighbors[] = new VertexContext($this->graph, $neighbor);
        }

        return $neighbors;
    }
}
