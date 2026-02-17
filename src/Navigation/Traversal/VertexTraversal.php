<?php

declare(strict_types=1);

namespace PhpArchitecture\Graph\Navigation\Traversal;

use PhpArchitecture\Graph\Graph;
use PhpArchitecture\Graph\Vertex\VertexInterface;

class VertexTraversal
{
    /**
     * @param VertexVisitorInterface[] $visitors
     */
    public function __construct(
        public readonly array $visitors,
    ) {}

    /**
     * @param ?callable(VertexInterface):bool $filter
     */
    public function traverse(Graph $graph, ?callable $filter = null): VertexTraversalResult
    {
        $result = new VertexTraversalResult();

        $stopAtCurrentEntity = false;
        foreach ($graph->vertexStore->getVertices($filter) as $vertex) {
            foreach ($this->visitors as $visitor) {
                $visitResult = $visitor->visit($vertex);
                $result->add($vertex->id(), $visitor::class, $visitResult);

                if ($visitResult->action === VisitAction::StopImmediately) {
                    break 2;
                }

                if ($visitResult->action === VisitAction::StopAtCurrentEntity) {
                    $stopAtCurrentEntity = true;
                }
            }

            if ($stopAtCurrentEntity) {
                break;
            }
        }

        return $result;
    }
}
