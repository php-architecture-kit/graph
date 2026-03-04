<?php

declare(strict_types=1);

namespace PhpArchitecture\Graph\Events\Listener;

use PhpArchitecture\Graph\Edge\EdgeInterface;

interface OnEdgeAddedInterface
{
    public function onEdgeAdded(EdgeInterface $edge): void;
}
