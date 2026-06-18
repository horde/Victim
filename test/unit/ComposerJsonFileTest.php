<?php

declare(strict_types=1);

namespace Horde\PhpConfigFile\Test\Unit;

use Horde\PhpConfigFile\PhpConfigFile;
use PHPUnit\Framework\TestCase;
use Stringable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversNothing;
use Horde\Victim\ComposerJsonFile;
use Horde\Victim\InvalidComposerJsonFileException;
use Horde\Victim\Support;
use Horde\Victim\SupportBuilder;

/**
 * @coversNothing
 */
#[CoversNothing]
class ComposerJsonFileTest extends TestCase
{
    public function testReadEmptyConfigFileThrowsException(): void
    {
        $this->expectException(InvalidComposerJsonFileException::class);
        $uut = new ComposerJsonFile(dirname(__DIR__, 1) . '/fixtures/empty/composer.json');
    }

    public function testGetSupportReturnsNullWhenMissing(): void
    {
        $json = '{"name": "test/package"}';
        $file = new ComposerJsonFile('/tmp/test.json', $json);

        $support = $file->getSupport();

        $this->assertNull($support);
    }

    public function testGetSupportReturnsObject(): void
    {
        $json = json_encode([
            'name' => 'test/package',
            'support' => [
                'email' => 'test@example.com',
                'issues' => 'https://github.com/test/issues',
            ],
        ]);
        $file = new ComposerJsonFile('/tmp/test.json', $json);

        $support = $file->getSupport();

        $this->assertInstanceOf(Support::class, $support);
        $this->assertSame('test@example.com', $support->email);
        $this->assertSame('https://github.com/test/issues', $support->issues);
    }

    public function testSetSupportStoresObject(): void
    {
        $json = '{"name": "test/package"}';
        $file = new ComposerJsonFile('/tmp/test.json', $json);

        $support = (new SupportBuilder())
            ->email('dev@lists.horde.org')
            ->issues('https://github.com/horde/horde/issues')
            ->build();

        $file->setSupport($support);

        $retrieved = $file->getSupport();
        $this->assertInstanceOf(Support::class, $retrieved);
        $this->assertSame('dev@lists.horde.org', $retrieved->email);
        $this->assertSame('https://github.com/horde/horde/issues', $retrieved->issues);
    }

    public function testSetSupportWithEmptyObjectRemovesField(): void
    {
        $json = json_encode([
            'name' => 'test/package',
            'support' => ['email' => 'test@example.com'],
        ]);
        $file = new ComposerJsonFile('/tmp/test.json', $json);

        $emptySupport = new Support();
        $file->setSupport($emptySupport);

        $this->assertNull($file->getSupport());
    }

    public function testSupportPersistsThroughSerialize(): void
    {
        $json = '{"name": "test/package"}';
        $file = new ComposerJsonFile('/tmp/test.json', $json);

        $support = (new SupportBuilder())
            ->email('dev@lists.horde.org')
            ->docs('https://docs.horde.org')
            ->build();

        $file->setSupport($support);
        $serialized = (string) $file;
        $decoded = json_decode($serialized, true);

        $this->assertArrayHasKey('support', $decoded);
        $this->assertSame('dev@lists.horde.org', $decoded['support']['email']);
        $this->assertSame('https://docs.horde.org', $decoded['support']['docs']);
        $this->assertArrayNotHasKey('issues', $decoded['support']);
    }

    public function testSetSupportOverwritesExisting(): void
    {
        $json = json_encode([
            'name' => 'test/package',
            'support' => [
                'email' => 'old@example.com',
                'issues' => 'https://old-issues.com',
            ],
        ]);
        $file = new ComposerJsonFile('/tmp/test.json', $json);

        $newSupport = (new SupportBuilder())
            ->email('new@example.com')
            ->docs('https://new-docs.com')
            ->build();

        $file->setSupport($newSupport);
        $retrieved = $file->getSupport();

        $this->assertSame('new@example.com', $retrieved->email);
        $this->assertSame('https://new-docs.com', $retrieved->docs);
        $this->assertNull($retrieved->issues);
    }

    public function testSupportWithAuthors(): void
    {
        $json = '{"name": "test/package"}';
        $file = new ComposerJsonFile('/tmp/test.json', $json);

        $support = (new SupportBuilder())
            ->email('dev@lists.horde.org')
            ->issues('https://github.com/test/issues')
            ->build();

        $file->setSupport($support);
        $serialized = (string) $file;
        $decoded = json_decode($serialized, true);

        $this->assertArrayHasKey('support', $decoded);
        $this->assertArrayHasKey('name', $decoded);
        $this->assertSame('test/package', $decoded['name']);
    }

    public function testSupportRoundTripThroughFileSaveLoad(): void
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'composer_test_');

        $original = new ComposerJsonFile($tempFile, '{"name": "test/package"}');
        $support = (new SupportBuilder())
            ->email('test@example.com')
            ->issues('https://github.com/test/issues')
            ->docs('https://docs.test.com')
            ->build();

        $original->setSupport($support);
        $original->save();

        $reloaded = new ComposerJsonFile($tempFile);
        $retrievedSupport = $reloaded->getSupport();

        $this->assertInstanceOf(Support::class, $retrievedSupport);
        $this->assertSame('test@example.com', $retrievedSupport->email);
        $this->assertSame('https://github.com/test/issues', $retrievedSupport->issues);
        $this->assertSame('https://docs.test.com', $retrievedSupport->docs);

        unlink($tempFile);
    }

    public function testSupportFieldOrderingInSerialization(): void
    {
        $json = '{"name": "test/package"}';
        $file = new ComposerJsonFile('/tmp/test.json', $json);

        $support = new Support(
            security: 'https://security.test.com',
            email: 'test@example.com',
            docs: 'https://docs.test.com',
        );

        $file->setSupport($support);
        $serialized = $support->__serialize();

        $keys = array_keys($serialized);
        $this->assertContains('email', $keys);
        $this->assertContains('docs', $keys);
        $this->assertContains('security', $keys);
    }
}
