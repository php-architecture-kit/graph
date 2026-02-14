<?php

declare(strict_types=1);

namespace PhpArchitecture\Graph\Events\Listener;

use PhpArchitecture\Graph\Vertex\Vertex;

interface OnVertexRemovedInterface
{
    public function onVertexRemoved(Vertex $vertex): void;
}
