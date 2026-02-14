<?php

declare(strict_types=1);

namespace PhpArchitecture\Graph\Edge\Validator;

use PhpArchitecture\Graph\Edge\Edge;
use PhpArchitecture\Graph\Edge\Exception\MultiEdgeException;
use PhpArchitecture\Graph\Graph;
use PhpArchitecture\Graph\Index\IncidenceIndex;

final class MultiEdgeValidator implements EdgeValidatorInterface
{
    public function validate(Edge $edge, Graph $graph): void
    {
        $incidence = $graph->indexRegistry->index(IncidenceIndex::class);
        $edgesToCheck = $incidence !== null
            ? $incidence->edgesFor($edge->u())
            : $graph->edgeStore->getAllEdges();

        foreach ($edgesToCheck as $existing) {
            $sameDirection = $existing->u()->equals($edge->u()) && $existing->v()->equals($edge->v());
            $reverseDirection = $existing->u()->equals($edge->v()) && $existing->v()->equals($edge->u());
            if ($sameDirection || $reverseDirection) {
                throw new MultiEdgeException('Multi-edge not allowed between `' . $edge->u()->toString() . '` and `' . $edge->v()->toString() . '`');
            }
        }
    }
}
