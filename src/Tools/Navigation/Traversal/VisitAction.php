<?php

declare(strict_types=1);

namespace PhpArchitecture\Graph\Tools\Navigation\Traversal;

enum VisitAction
{
    case Continue;
    case StopImmediately;
    case StopAtCurrentEntity;
}
