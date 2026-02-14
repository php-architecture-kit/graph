<?php

declare(strict_types=1);

namespace PhpArchitecture\Graph\Events\Listener;

use PhpArchitecture\Graph\Vertex\Vertex;

interface OnVertexAddedInterface
{
    public function onVertexAdded(Vertex $vertex): void;
}
