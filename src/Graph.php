<?php

declare(strict_types=1);

namespace PhpArchitecture\Graph;

use PhpArchitecture\Graph\Config\GraphConfig;
use PhpArchitecture\Graph\Edge\EdgeStore;
use PhpArchitecture\Graph\Edge\Validator\CyclicEdgeValidator;
use PhpArchitecture\Graph\Edge\Validator\EdgeValidatorChain;
use PhpArchitecture\Graph\Edge\Validator\EdgeValidatorInterface;
use PhpArchitecture\Graph\Edge\Validator\MultiEdgeValidator;
use PhpArchitecture\Graph\Edge\Validator\SelfLoopValidator;
use PhpArchitecture\Graph\Edge\Weight\EdgeWeightStore;
use PhpArchitecture\Graph\Events\EventDispatcher;
use PhpArchitecture\Graph\Index\IncidenceIndex;
use PhpArchitecture\Graph\Index\IndexRegistry;
use PhpArchitecture\Graph\Vertex\VertexStore;

class Graph
{
    public readonly GraphConfig $config;
    public readonly EdgeStore $edgeStore;
    public readonly VertexStore $vertexStore;
    public readonly EventDispatcher $eventDispatcher;
    public readonly IndexRegistry $indexRegistry;

    public function __construct(
        ?GraphConfig $config = null,
        ?EdgeStore $edgeStore = null,
        ?VertexStore $vertexStore = null,
        ?IndexRegistry $indexRegistry = null,
    ) {
        $this->config = $config ?? new GraphConfig();
        $this->initializeIndexRegistry($indexRegistry);
        $this->initializeEventDispatcher();

        $this->initializeEdgeStore($edgeStore);
        $this->initializeVertexStore($vertexStore);
    }

    private function initializeIndexRegistry(?IndexRegistry $indexRegistry): void
    {
        $this->indexRegistry = $indexRegistry ?? new IndexRegistry();
        $this->indexRegistry->register(new IncidenceIndex());
    }

    private function initializeEventDispatcher(): void
    {
        $this->eventDispatcher = new EventDispatcher($this->indexRegistry);
    }

    private function initializeVertexStore(?VertexStore $vertexStore): void
    {
        $this->vertexStore = $vertexStore ?? new VertexStore([], $this->eventDispatcher);
        $this->vertexStore->setGraph($this);
    }

    private function initializeEdgeStore(?EdgeStore $edgeStore): void
    {
        $edgeValidator = $this->buildEdgeValidator($this->config);

        $defaultWeightStore = $this->config->weightConfig !== null
            ? new EdgeWeightStore($this->config->weightConfig)
            : null;

        if ($edgeStore !== null && $this->config->usesEdgeWeights() && !$edgeStore->hasWeightStore()) {
            $edgeStore = new EdgeStore($edgeStore->getEdges(), $this->eventDispatcher, $edgeValidator, $defaultWeightStore);
        }

        $this->edgeStore = $edgeStore ?? new EdgeStore(
            store: [],
            eventDispatcher: $this->eventDispatcher,
            edgeValidator: $edgeValidator,
            weightStore: $defaultWeightStore,
        );

        $this->edgeStore->setGraph($this);
        $this->edgeStore->populateEdgeDefaultWeights();
    }

    private function buildEdgeValidator(GraphConfig $config): EdgeValidatorInterface
    {
        $chain = new EdgeValidatorChain();

        if (!$config->allowSelfLoop) {
            $chain->addValidator(new SelfLoopValidator());
        }
        if (!$config->allowMultiEdge) {
            $chain->addValidator(new MultiEdgeValidator());
        }
        if (!$config->allowCyclicEdge) {
            $chain->addValidator(new CyclicEdgeValidator());
        }

        return $chain;
    }
}
