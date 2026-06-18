<?php

namespace Horde\Victim;

use stdClass;

interface RepositoryDefinition
{
    public function getType(): string;
    public function getUrl(): string;

    public function dumpStdClass();
}
