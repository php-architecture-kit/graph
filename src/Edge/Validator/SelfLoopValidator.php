<?php

declare(strict_types=1);

namespace PhpArchitecture\Graph\Edge\Validator;

use PhpArchitecture\Graph\Edge\Edge;
use PhpArchitecture\Graph\Edge\Exception\SelfLoopException;
use PhpArchitecture\Graph\Graph;

final class SelfLoopValidator implements EdgeValidatorInterface
{
    public function validate(Edge $edge, Graph $graph): void
    {
        if ($edge->u()->equals($edge->v())) {
            throw new SelfLoopException('Self-loop not allowed for vertex `' . $edge->u()->toString() . '`');
        }
    }
}
