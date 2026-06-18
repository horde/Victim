<?php

declare(strict_types=1);

namespace Horde\Victim;

use Stringable;
use DirectoryIterator;

use function file_exists;
use function is_file;

/**
 *
 */
class VendorPackageIterator extends DirectoryIterator implements VendorPackage
{
    public function __construct(
        string|Stringable|DirectoryIterator $vendorRoot
    ) {
        parent::__construct($vendorRoot . '/vendor');
    }

    public function hasFile(string|Stringable $relativePath): bool
    {
        $path = $this->getRealPath() . (string) $relativePath;
        return (file_exists($path));
    }

}
