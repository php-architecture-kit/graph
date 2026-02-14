<?php

declare(strict_types=1);

namespace PhpArchitecture\Graph\Edge\Validator;

use PhpArchitecture\Graph\Edge\Edge;
use PhpArchitecture\Graph\Graph;

interface EdgeValidatorInterface
{
    /**
     * Validates the edge before adding to the graph.
     *
     * @throws \PhpArchitecture\Graph\Exception\GraphException
     */
    public function validate(Edge $edge, Graph $graph): void;
}
