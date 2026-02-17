# php-architecture-kit/graph

Framework-agnostic graph library for PHP applications. It provides a consistent API for building graphs with vertices and directed/undirected edges, traversing graph elements with visitors, and calculating shortest paths between vertices.

## Features

- **Directed and undirected edges** - Use `DirectedEdge` and `UndirectedEdge` in the same graph
- **Strongly typed identities** - `VertexId` and `EdgeId` value objects
- **Configurable validation** - Toggle self-loop, multi-edge, and cyclic-edge rules
- **Optional named edge weights** - Define defaults per edge class and override per edge
- **Traversal API (Visitor Pattern)** - Traverse vertices/edges with stop control
- **Navigation API** - Select entities as contexts and query neighbors/incident edges
- **Shortest path finder** - Bidirectional shortest path search with optional edge filtering
- **Automatic incidence index updates** - Built-in index synchronization through graph events

## Installation

```bash
composer require php-architecture-kit/graph
```

## Quick Start

```php
use PhpArchitecture\Graph\Graph;
use PhpArchitecture\Graph\GraphNavigator;
use PhpArchitecture\Graph\Edge\DirectedEdge;
use PhpArchitecture\Graph\Edge\UndirectedEdge;
use PhpArchitecture\Graph\Vertex\Vertex;

$graph = new Graph();

$a = new Vertex(metadata: ['name' => 'A']);
$b = new Vertex(metadata: ['name' => 'B']);
$c = new Vertex(metadata: ['name' => 'C']);

$graph->vertexStore->addVertex($a);
$graph->vertexStore->addVertex($b);
$graph->vertexStore->addVertex($c);

$ab = new DirectedEdge($a, $b);
$bc = new DirectedEdge($b, $c);
$ac = new UndirectedEdge($a, $c);

$graph->edgeStore->addEdge($ab);
$graph->edgeStore->addEdge($bc);
$graph->edgeStore->addEdge($ac);

$isAdjacent = $graph->edgeStore->areAdjacent($a->id(), $b->id()); // true
$degreeOfB = $graph->edgeStore->degree($b->id()); // 2

$navigator = new GraphNavigator($graph);
$path = $navigator->shortestPathTo($a->id(), $c->id()); // EdgeContext[]
```

## Graph Configuration

Use `GraphConfig` to control validation behavior and edge weights.

```php
use PhpArchitecture\Graph\Graph;
use PhpArchitecture\Graph\GraphConfig;
use PhpArchitecture\Graph\Edge\DirectedEdge;
use PhpArchitecture\Graph\Edge\UndirectedEdge;
use PhpArchitecture\Graph\EdgeWeight\Config\WeightConfig;

$graph = new Graph(config: new GraphConfig(
    allowSelfLoop: false,
    allowMultiEdge: false,
    allowCyclicEdge: false,
    weightConfig: new WeightConfig([
        DirectedEdge::class => ['cost' => 1.0, 'latency' => 10.0],
        UndirectedEdge::class => ['cost' => 0.5],
    ]),
));
```

## Navigation and Context API

`GraphNavigator` is the main read/query entry point.

### Selecting Vertices and Edges

```php
use PhpArchitecture\Graph\GraphNavigator;
use PhpArchitecture\Graph\Vertex\VertexInterface;

$navigator = new GraphNavigator($graph);

$vertexContext = $navigator->selectVertex($a->id());
$edgeContext = $navigator->selectEdge($ab->id());

$allVertexContexts = $navigator->selectVertices();
$apiVertices = $navigator->selectVertices(
    static fn(VertexInterface $vertex): bool => ($vertex->metadata['type'] ?? null) === 'api',
);
```

### Working with Context Objects

```php
// VertexContext
$neighborContexts = $vertexContext->neighbors();
$incidentEdges = $vertexContext->edges();

// EdgeContext
$isDirected = $edgeContext->isDirected();
$u = $edgeContext->u();
$v = $edgeContext->v();
```

## Traversal (Visitor Pattern)

The traversal module supports multiple visitors and three control actions:

- `VisitAction::Continue`
- `VisitAction::StopAtCurrentEntity`
- `VisitAction::StopImmediately`

```php
use PhpArchitecture\Graph\Navigation\Traversal\VertexVisitorInterface;
use PhpArchitecture\Graph\Navigation\Traversal\VisitAction;
use PhpArchitecture\Graph\Navigation\Traversal\VisitResult;
use PhpArchitecture\Graph\Vertex\VertexInterface;

final class CollectVertexIdsVisitor implements VertexVisitorInterface
{
    /** @var list<string> */
    public array $visited = [];

    public function visit(VertexInterface $vertex): VisitResult
    {
        $this->visited[] = $vertex->id()->toString();

        return new VisitResult(VisitAction::Continue);
    }
}

$visitor = new CollectVertexIdsVisitor();
$result = $navigator->traverseVertices([$visitor]);

$visitedByVisitor = $result->getByVisitor(CollectVertexIdsVisitor::class);
```

## Shortest Path

Shortest path search is available via `GraphNavigator::shortestPathTo(...)` and uses bidirectional search internally.

```php
use PhpArchitecture\Graph\Edge\DirectedEdgeInterface;
use PhpArchitecture\Graph\Edge\UndirectedEdgeInterface;

$path = $navigator->shortestPathTo(
    sourceId: $a->id(),
    targetId: $c->id(),
    edgeFilter: static fn(DirectedEdgeInterface|UndirectedEdgeInterface $edge): bool =>
        ($edge->metadata['blocked'] ?? false) === false,
);

$pathEdgeIds = array_map(
    static fn($context): string => $context->edge->id()->toString(),
    $path,
);
```

Returns:

- `EdgeContext[]` when a path exists
- `[]` when source equals target
- `[]` when no path matches the filter

## Edge Weights

If `weightConfig` is enabled, you can define and read named weights per edge.

```php
use PhpArchitecture\Graph\Edge\DirectedEdge;
use PhpArchitecture\Graph\EdgeWeight\EdgeWeights;
use PhpArchitecture\Graph\EdgeWeight\Weight;

$edge = new DirectedEdge($a, $b);

$graph->edgeStore->addEdge(
    $edge,
    new EdgeWeights($edge->id(), [
        'cost' => new Weight('cost', 3.0),
        'latency' => new Weight('latency', 25.0),
    ]),
);

$cost = $navigator->selectEdge($edge->id())->weights()->value('cost');
```

## API Reference

### GraphNavigator

| Method | Description |
|--------|-------------|
| `selectVertex(VertexId $id): VertexContext` | Select one vertex as context |
| `selectVertices(?callable $filter = null): VertexContext[]` | Select many vertices with optional filter |
| `selectEdge(EdgeId $id): EdgeContext` | Select one edge as context |
| `selectEdges(?callable $filter = null): EdgeContext[]` | Select many edges with optional filter |
| `shortestPathTo(VertexId $sourceId, VertexId $targetId, ?callable $edgeFilter = null): EdgeContext[]` | Find shortest path between two vertices |
| `traverseVertices(array $visitors, ?callable $filter = null): VertexTraversalResult` | Traverse vertices with visitors |
| `traverseEdges(array $visitors, ?callable $filter = null): EdgeTraversalResult` | Traverse edges with visitors |

### VertexContext

| Method | Description |
|--------|-------------|
| `edges(?callable $filter = null): EdgeContext[]` | Get incident edges for this vertex |
| `neighbors(?callable $edgeFilter = null, ?callable $filter = null): VertexContext[]` | Get neighboring vertices |

### EdgeContext

| Method | Description |
|--------|-------------|
| `isDirected(): bool` | Check if edge is directed |
| `isUndirected(): bool` | Check if edge is undirected |
| `u(): VertexInterface` | Resolve first endpoint |
| `v(): VertexInterface` | Resolve second endpoint |
| `weights(): EdgeWeights` | Resolve edge weights (when configured) |

### Traversal Interfaces

| Interface | Method |
|-----------|--------|
| `VertexVisitorInterface` | `visit(VertexInterface $vertex): VisitResult` |
| `EdgeVisitorInterface` | `visit(DirectedEdgeInterface\|UndirectedEdgeInterface $edge): VisitResult` |

## Testing

Package is tested with PHPUnit in the [php-architecture-kit/workspace](https://github.com/php-architecture-kit/workspace) project.

## License

MIT
