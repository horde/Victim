<?php

declare(strict_types=1);

namespace Horde\Victim\Test\Unit;

use PHPUnit\Framework\TestCase;

/**
 * Deliberately version-sensitive test used to prove the CI matrix surfaces
 * a per-PHP-version failure.
 *
 * The suite was validated against PHP 8.3, 8.4 and 8.5. The assertion below
 * derives the running interpreter's minor version from PHP_VERSION_ID and
 * requires it to fall inside that validated window. It therefore passes on
 * every lane up to 8.5 and genuinely cannot pass on 8.6, so exactly the 8.6
 * lanes go red while the rest stay green.
 *
 * PHP_VERSION_ID is MAJOR * 10000 + MINOR * 100 + PATCH, so integer division
 * by 100 then modulo 100 recovers the minor component.
 */
class PhpVersionMatrixTest extends TestCase
{
    private const HIGHEST_VALIDATED_MINOR = 5;

    public function testRunsOnAValidatedPhpMinorVersion(): void
    {
        $minor = intdiv(PHP_VERSION_ID, 100) % 100;

        $this->assertLessThanOrEqual(
            self::HIGHEST_VALIDATED_MINOR,
            $minor,
            sprintf(
                'Test suite is validated up to PHP 8.%d; running under 8.%d.',
                self::HIGHEST_VALIDATED_MINOR,
                $minor,
            ),
        );
    }
}
