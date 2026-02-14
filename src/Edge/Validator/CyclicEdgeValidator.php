<?php

declare(strict_types=1);

namespace PhpArchitecture\Graph\Edge\Validator;

use PhpArchitecture\Graph\Edge\Edge;
use PhpArchitecture\Graph\Edge\Exception\CyclicEdgeException;
use PhpArchitecture\Graph\Graph;
use PhpArchitecture\Graph\Index\IncidenceIndex;
use PhpArchitecture\Graph\Vertex\Identity\VertexId;
use SplQueue;

final class CyclicEdgeValidator implements EdgeValidatorInterface
{
    public function validate(Edge $edge, Graph $graph): void
    {
        if ($this->wouldCreateCycle($edge, $graph)) {
            throw new CyclicEdgeException('Adding edge would create a cycle from `' . $edge->u()->toString() . '` to `' . $edge->v()->toString() . '`');
        }
    }

    private function wouldCreateCycle(Edge $edge, Graph $graph): bool
    {
        $source = $edge->v();
        $target = $edge->u();

        if ($source->equals($target)) {
            return true;
        }

        $visited = [$source->toString() => true];
        $queue = new \SplQueue();
        $queue->enqueue($source);

        while (!$queue->isEmpty()) {
            $current = $queue->dequeue();
            foreach ($this->outgoingEdges($current, $graph) as $outEdge) {
                $neighbor = $outEdge->v();
                if ($neighbor->equals($target)) {
                    return true;
                }
                $key = $neighbor->toString();
                if (!isset($visited[$key])) {
                    $visited[$key] = true;
                    $queue->enqueue($neighbor);
                }
            }
        }

        return false;
    }

    /**
     * @return array<string,Edge>
     */
    private function outgoingEdges(VertexId $vertex, Graph $graph): array
    {
        $incidence = $graph->indexRegistry->index(IncidenceIndex::class);
        $edges = $incidence !== null
            ? $incidence->edgesFor($vertex)
            : $graph->edgeStore->getAllEdges();

        return array_filter(
            $edges,
            static fn(Edge $e): bool => $e->u()->equals($vertex),
        );
    }
}
