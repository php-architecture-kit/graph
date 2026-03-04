<?php

declare(strict_types=1);

namespace PhpArchitecture\Graph\Edge;

enum EdgeType: string
{
    case Directed = 'directed';
    case Undirected = 'undirected';
}
