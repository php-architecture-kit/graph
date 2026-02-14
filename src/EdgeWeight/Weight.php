<?php

declare(strict_types=1);

namespace PhpArchitecture\Graph\EdgeWeight;

use InvalidArgumentException;

final readonly class Weight
{
    public function __construct(
        public string $key,
        public float $value,
    ) {
        if ($key === '') {
            throw new \InvalidArgumentException('Weight key must not be empty.');
        }

        if (!is_finite($value)) {
            throw new \InvalidArgumentException('Weight value must be finite.');
        }
    }
}
