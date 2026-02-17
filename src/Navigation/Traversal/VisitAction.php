<?php

declare(strict_types=1);

namespace PhpArchitecture\Graph\Navigation\Traversal;

enum VisitAction
{
    case Continue;
    case StopImmediately;
    case StopAtCurrentEntity;
}
