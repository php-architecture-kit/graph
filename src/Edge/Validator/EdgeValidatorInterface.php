<?php

declare(strict_types=1);

namespace PhpArchitecture\Graph\Edge\Validator;

use PhpArchitecture\Graph\Edge\DirectedEdgeInterface;
use PhpArchitecture\Graph\Edge\UndirectedEdgeInterface;
use PhpArchitecture\Graph\Graph;

interface EdgeValidatorInterface
{
    /**
     * Validates the edge before adding to the graph.
     *
     * @throws \PhpArchitecture\Graph\Exception\GraphException
     */
    public function validate(DirectedEdgeInterface|UndirectedEdgeInterface $edge, Graph $graph): void;
}
