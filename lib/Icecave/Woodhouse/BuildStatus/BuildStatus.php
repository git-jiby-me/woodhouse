<?php
namespace Icecave\Woodhouse\BuildStatus;

use Eloquent\Enumeration\Enumeration;

class BuildStatus extends Enumeration
{
    const PASSING = 'passing';
    const FAILING = 'failing';
    const PENDING = 'pending';
    const UNKNOWN = 'unknown';
    const ERROR   = 'error';
}
