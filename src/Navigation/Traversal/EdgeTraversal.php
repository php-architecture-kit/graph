<?php

declare(strict_types=1);

namespace PhpArchitecture\Graph\Navigation\Traversal;

use PhpArchitecture\Graph\Graph;
use PhpArchitecture\Graph\Edge\DirectedEdgeInterface;
use PhpArchitecture\Graph\Edge\UndirectedEdgeInterface;

class EdgeTraversal
{
    /**
     * @param EdgeVisitorInterface[] $visitors
     */
    public function __construct(
        public readonly array $visitors,
    ) {}

    /**
     * @param ?callable(DirectedEdgeInterface|UndirectedEdgeInterface):bool $filter
     */
    public function traverse(Graph $graph, ?callable $filter = null): EdgeTraversalResult
    {
        $result = new EdgeTraversalResult();

        $stopAtCurrentEntity = false;
        foreach ($graph->edgeStore->getEdges($filter) as $edge) {
            foreach ($this->visitors as $visitor) {
                $visitResult = $visitor->visit($edge);
                $result->add($edge->id(), $visitor::class, $visitResult);

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
