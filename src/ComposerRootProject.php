<?php

declare(strict_types=1);

namespace Horde\Victim;

use Stringable;
use DirectoryIterator;

class ComposerRootProject implements Stringable
{
    public readonly DirectoryIterator $projectRoot;
    public function __construct(
        string|Stringable|DirectoryIterator $projectRoot
    ) {
        $this->projectRoot = new DirectoryIterator($projectRoot);
    }
    public function __toString(): string
    {
        return $this->projectRoot->getRealPath();
    }

    public function getComposerJsonFile(): ComposerJsonFile
    {
        return new ComposerJsonFile($this->projectRoot->getRealPath() . '/composer.json');
    }

    public function getVendorDirIterator(): VendorDirIterator
    {
        return new VendorDirIterator($this);
    }

    public function getVendorPackageIterator(string|Stringable $vendor): VendorPackageIterator
    {
        return new VendorPackageIterator($this->projectRoot->getRealPath() . '/vendor/' . $vendor);
    }
}
