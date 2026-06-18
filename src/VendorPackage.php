<?php

declare(strict_types=1);

namespace Horde\Victim;

use Stringable;

interface VendorPackage
{
    public function hasFile(string|Stringable $relativePath): bool;
}
