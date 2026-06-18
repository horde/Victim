<?php

declare(strict_types=1);

namespace Horde\Victim;

enum FileType: string
{
    case HordeYml = '.horde.yml';
    case ComposerJson = 'composer.json';
    case PackageXml = 'package.xml';
    case PackageJson = 'package.json';
    case PackageYml = 'package.yml';
}
