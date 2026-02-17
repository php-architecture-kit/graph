<?php

declare(strict_types=1);

namespace PhpArchitecture\Graph\EdgeWeight\Config;

use InvalidArgumentException;
use PhpArchitecture\Graph\Edge\DirectedEdgeInterface;
use PhpArchitecture\Graph\Edge\Identity\EdgeId;
use PhpArchitecture\Graph\Edge\UndirectedEdgeInterface;
use PhpArchitecture\Graph\EdgeWeight\EdgeWeights;
use PhpArchitecture\Graph\EdgeWeight\Weight;

final class WeightConfig
{
    /** @var array<class-string<DirectedEdgeInterface|UndirectedEdgeInterface>,array<string,float>> */
    private array $defaultsByEdgeClass = [];

    /** @var array<class-string<DirectedEdgeInterface|UndirectedEdgeInterface>,array<string,float>> */
    private array $resolvedDefaultsByEdgeClass = [];

    /**
     * @param array<class-string<DirectedEdgeInterface|UndirectedEdgeInterface>,array<string,float|int>> $defaultsByEdgeClass
     */
    public function __construct(array $defaultsByEdgeClass = [])
    {
        foreach ($defaultsByEdgeClass as $edgeClass => $defaultWeights) {
            $this->define($edgeClass, $defaultWeights);
        }
    }

    /**
     * @param class-string<DirectedEdgeInterface|UndirectedEdgeInterface> $edgeClass
     * @param array<string,float|int> $defaultWeights
     */
    public function define(string $edgeClass, array $defaultWeights): self
    {
        if (!is_a($edgeClass, DirectedEdgeInterface::class, true) && !is_a($edgeClass, UndirectedEdgeInterface::class, true)) {
            throw new \InvalidArgumentException('Weight defaults can be defined only for `' . DirectedEdgeInterface::class . '` or `' . UndirectedEdgeInterface::class . '` subclasses.');
        }

        $normalized = [];
        foreach ($defaultWeights as $key => $value) {
            if (!is_string($key) || $key === '') {
                throw new \InvalidArgumentException('Weight key must be a non-empty string.');
            }

            $normalizedValue = (float) $value;
            if (!is_finite($normalizedValue)) {
                throw new \InvalidArgumentException('Weight value for key `' . $key . '` must be finite.');
            }

            $normalized[$key] = $normalizedValue;
        }

        $this->defaultsByEdgeClass[$edgeClass] = $normalized;
        $this->resolvedDefaultsByEdgeClass = [];

        return $this;
    }

    /** @param class-string<DirectedEdgeInterface|UndirectedEdgeInterface> $edgeClass */
    public function default(string $edgeClass): EdgeWeights
    {
        if (!is_a($edgeClass, DirectedEdgeInterface::class, true) && !is_a($edgeClass, UndirectedEdgeInterface::class, true)) {
            throw new \InvalidArgumentException('Weight defaults can be resolved only for `' . DirectedEdgeInterface::class . '` or `' . UndirectedEdgeInterface::class . '` subclasses.');
        }

        $defaultWeights = [];
        foreach ($this->resolveDefaultWeights($edgeClass) as $key => $value) {
            $defaultWeights[$key] = new Weight($key, $value);
        }

        return new EdgeWeights(EdgeId::nil(), $defaultWeights);
    }

    /** @return array<class-string<DirectedEdgeInterface|UndirectedEdgeInterface>,array<string,float>> */
    public function all(): array
    {
        return $this->defaultsByEdgeClass;
    }

    /**
     * @param class-string<DirectedEdgeInterface|UndirectedEdgeInterface> $edgeClass
     * @return array<string,float>
     */
    private function resolveDefaultWeights(string $edgeClass): array
    {
        if (isset($this->resolvedDefaultsByEdgeClass[$edgeClass])) {
            return $this->resolvedDefaultsByEdgeClass[$edgeClass];
        }

        /** @var array<int,array{distance:int,defaults:array<string,float>}> $matches */
        $matches = [];

        foreach ($this->defaultsByEdgeClass as $configuredClass => $defaults) {
            $distance = $this->inheritanceDistance($edgeClass, $configuredClass);
            if ($distance === null) {
                continue;
            }

            $matches[] = [
                'distance' => $distance,
                'defaults' => $defaults,
            ];
        }

        usort(
            $matches,
            static fn(array $left, array $right): int => $right['distance'] <=> $left['distance'],
        );

        $resolved = [];
        foreach ($matches as $match) {
            $resolved = array_replace($resolved, $match['defaults']);
        }

        $this->resolvedDefaultsByEdgeClass[$edgeClass] = $resolved;

        return $resolved;
    }

    /**
     * @param class-string<DirectedEdgeInterface|UndirectedEdgeInterface> $edgeClass
     * @param class-string<DirectedEdgeInterface|UndirectedEdgeInterface> $candidateClass
     */
    private function inheritanceDistance(string $edgeClass, string $candidateClass): ?int
    {
        if ($edgeClass === $candidateClass) {
            return 0;
        }

        $distance = 0;
        $currentClass = $edgeClass;

        while (($parentClass = get_parent_class($currentClass)) !== false) {
            $distance++;

            if ($parentClass === $candidateClass) {
                return $distance;
            }

            $currentClass = $parentClass;
        }

        return null;
    }
}
