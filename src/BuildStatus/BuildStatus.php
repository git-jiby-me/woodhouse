<?php
namespace Icecave\Woodhouse\BuildStatus;

use Eloquent\Enumeration\AbstractEnumeration;

class BuildStatus extends AbstractEnumeration
{
    const PASSING = 'passing';
    const FAILING = 'failing';
    const PENDING = 'pending';
    const UNKNOWN = 'unknown';
    const ERROR   = 'error';
}
