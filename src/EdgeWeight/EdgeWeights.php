<?php

declare(strict_types=1);

namespace PhpArchitecture\Graph\EdgeWeight;

use InvalidArgumentException;
use PhpArchitecture\Graph\Edge\Identity\EdgeId;
use PhpArchitecture\Graph\EdgeWeight\Exception\WeightNotFoundException;

final class EdgeWeights
{
    /**
     * @param array<string,Weight> $weightsByKey
     */
    public function __construct(
        public readonly EdgeId $edgeId,
        private array $weightsByKey = [],
    ) {}

    /** @return array<string,Weight> */
    public function all(): array
    {
        return $this->weightsByKey;
    }

    public function get(string $key): Weight
    {
        if (!isset($this->weightsByKey[$key])) {
            throw new WeightNotFoundException('Weight `' . $key . '` does not exist for edge `' . $this->edgeId->toString() . '`.');
        }

        return $this->weightsByKey[$key];
    }

    public function has(string $key): bool
    {
        return isset($this->weightsByKey[$key]);
    }

    public function value(string $key): float
    {
        return $this->get($key)->value;
    }

    public function set(Weight $weight): void
    {
        $this->weightsByKey[$weight->key] = $weight;
    }

    public function replace(Weight $weight): void
    {
        if (!isset($this->weightsByKey[$weight->key])) {
            throw new WeightNotFoundException('Weight `' . $weight->key . '` does not exist for edge `' . $this->edgeId->toString() . '`.');
        }

        $this->weightsByKey[$weight->key] = $weight;
    }

    public function remove(string $key): void
    {
        unset($this->weightsByKey[$key]);
    }

    public function clear(): void
    {
        $this->weightsByKey = [];
    }

    /**
     * @param Weight[] $weights
     */
    public function fillWith(array $weights, bool $onlyMissing = true): self
    {
        foreach ($weights as $weight) {
            if (!$weight instanceof Weight) {
                throw new \InvalidArgumentException('Each item passed to fillWith must be instance of ' . Weight::class . '.');
            }

            if ($onlyMissing && $this->has($weight->key)) {
                continue;
            }

            $this->set($weight);
        }

        return $this;
    }

    public function withEdgeId(EdgeId $edgeId): self
    {
        return new self($edgeId, $this->weightsByKey);
    }
}
