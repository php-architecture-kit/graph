<?php

declare(strict_types=1);

namespace PhpArchitecture\Graph;

use PhpArchitecture\Graph\Edge\EdgeStore;
use PhpArchitecture\Graph\Edge\Validator\CyclicEdgeValidator;
use PhpArchitecture\Graph\Edge\Validator\EdgeValidatorChain;
use PhpArchitecture\Graph\Edge\Validator\EdgeValidatorInterface;
use PhpArchitecture\Graph\Edge\Validator\MultiEdgeValidator;
use PhpArchitecture\Graph\Edge\Validator\SelfLoopValidator;
use PhpArchitecture\Graph\EdgeWeight\EdgeWeightStore;
use PhpArchitecture\Graph\Events\EventDispatcher;
use PhpArchitecture\Graph\Index\IncidenceIndex;
use PhpArchitecture\Graph\Index\IndexNotifier;
use PhpArchitecture\Graph\Index\IndexRegistry;
use PhpArchitecture\Graph\Vertex\VertexStore;

class Graph
{
    public readonly GraphConfig $config;
    public readonly EdgeValidatorInterface $edgeValidator;
    public readonly VertexStore $vertexStore;
    public readonly EdgeStore $edgeStore;
    public readonly IndexRegistry $indexRegistry;
    public readonly EventDispatcher $eventDispatcher;
    public readonly IndexNotifier $indexNotifier;

    public function __construct(
        ?GraphConfig $config = null,
        ?IndexRegistry $indexRegistry = null,
        ?VertexStore $vertexStore = null,
        ?EdgeStore $edgeStore = null,
    ) {
        $this->config = $config ?? new GraphConfig();
        $this->edgeValidator = $this->buildEdgeValidator($this->config);

        $this->indexRegistry = $indexRegistry ?? new IndexRegistry();
        $this->indexRegistry->register(new IncidenceIndex());

        $this->eventDispatcher = new EventDispatcher();
        $this->eventDispatcher->addOnVertexAddedListener($this->indexRegistry);
        $this->eventDispatcher->addOnVertexRemovedListener($this->indexRegistry);
        $this->eventDispatcher->addOnEdgeAddedListener($this->indexRegistry);
        $this->eventDispatcher->addOnEdgeRemovedListener($this->indexRegistry);

        $this->indexNotifier = new IndexNotifier($this->eventDispatcher);

        $this->vertexStore = $vertexStore ?? new VertexStore();
        $this->vertexStore->setGraph($this);

        $defaultWeightStore = $this->config->weightConfig !== null
            ? new EdgeWeightStore($this->config->weightConfig)
            : null;

        if ($edgeStore !== null && $this->config->usesEdgeWeights() && !$edgeStore->hasWeightStore()) {
            $edgeStore = new EdgeStore($edgeStore->getAllEdges(), $defaultWeightStore);
        }

        $this->edgeStore = $edgeStore ?? new EdgeStore(weightStore: $defaultWeightStore);
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
