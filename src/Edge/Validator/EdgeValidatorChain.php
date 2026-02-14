<?php

declare(strict_types=1);

namespace PhpArchitecture\Graph\Edge\Validator;

use PhpArchitecture\Graph\Edge\Edge;
use PhpArchitecture\Graph\Graph;

final class EdgeValidatorChain implements EdgeValidatorInterface
{
    /**
     * @param EdgeValidatorInterface[] $validators
     */
    public function __construct(
        private array $validators = [],
    ) {
    }

    public function addValidator(EdgeValidatorInterface $validator): self
    {
        $this->validators[] = $validator;

        return $this;
    }

    public function validate(Edge $edge, Graph $graph): void
    {
        foreach ($this->validators as $validator) {
            $validator->validate($edge, $graph);
        }
    }
}
