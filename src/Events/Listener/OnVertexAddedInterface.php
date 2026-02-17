<?php

declare(strict_types=1);

namespace PhpArchitecture\Graph\Events\Listener;

use PhpArchitecture\Graph\Vertex\VertexInterface;

interface OnVertexAddedInterface
{
    public function onVertexAdded(VertexInterface $vertex): void;
}
