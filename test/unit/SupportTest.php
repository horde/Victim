<?php

declare(strict_types=1);

namespace Horde\Victim\Test\Unit;

use Horde\Victim\Support;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use stdClass;

#[CoversClass(Support::class)]
class SupportTest extends TestCase
{
    public function testConstructorWithNoFields(): void
    {
        $support = new Support();

        $this->assertNull($support->email);
        $this->assertNull($support->issues);
        $this->assertNull($support->forum);
        $this->assertNull($support->wiki);
        $this->assertNull($support->chat);
        $this->assertNull($support->docs);
        $this->assertNull($support->source);
        $this->assertNull($support->irc);
        $this->assertNull($support->rss);
        $this->assertNull($support->security);
    }

    public function testConstructorWithAllFields(): void
    {
        $support = new Support(
            email: 'dev@lists.horde.org',
            issues: 'https://github.com/horde/horde/issues',
            forum: 'https://forum.horde.org',
            wiki: 'https://wiki.horde.org',
            chat: 'https://gitter.im/horde/community',
            docs: 'https://www.horde.org/libraries/Horde_Http',
            source: 'https://github.com/horde/Http',
            irc: 'irc://irc.freenode.net/horde',
            rss: 'https://www.horde.org/feed.xml',
            security: 'https://www.horde.org/security',
        );

        $this->assertSame('dev@lists.horde.org', $support->email);
        $this->assertSame('https://github.com/horde/horde/issues', $support->issues);
        $this->assertSame('https://forum.horde.org', $support->forum);
        $this->assertSame('https://wiki.horde.org', $support->wiki);
        $this->assertSame('https://gitter.im/horde/community', $support->chat);
        $this->assertSame('https://www.horde.org/libraries/Horde_Http', $support->docs);
        $this->assertSame('https://github.com/horde/Http', $support->source);
        $this->assertSame('irc://irc.freenode.net/horde', $support->irc);
        $this->assertSame('https://www.horde.org/feed.xml', $support->rss);
        $this->assertSame('https://www.horde.org/security', $support->security);
    }

    public function testConstructorWithPartialFields(): void
    {
        $support = new Support(
            email: 'test@example.com',
            issues: 'https://github.com/test/issues',
            docs: 'https://docs.test.com',
        );

        $this->assertSame('test@example.com', $support->email);
        $this->assertSame('https://github.com/test/issues', $support->issues);
        $this->assertSame('https://docs.test.com', $support->docs);
        $this->assertNull($support->forum);
        $this->assertNull($support->wiki);
        $this->assertNull($support->chat);
        $this->assertNull($support->source);
        $this->assertNull($support->irc);
        $this->assertNull($support->rss);
        $this->assertNull($support->security);
    }

    public function testConstructorWithNamedParametersInDifferentOrder(): void
    {
        $support = new Support(
            docs: 'https://docs.test.com',
            email: 'test@example.com',
            source: 'https://github.com/test/repo',
        );

        $this->assertSame('test@example.com', $support->email);
        $this->assertSame('https://docs.test.com', $support->docs);
        $this->assertSame('https://github.com/test/repo', $support->source);
    }

    public function testFromStdClass(): void
    {
        $data = new stdClass();
        $data->email = 'test@example.com';
        $data->issues = 'https://github.com/test/issues';
        $data->docs = 'https://docs.test.com';

        $support = Support::fromStdClass($data);

        $this->assertSame('test@example.com', $support->email);
        $this->assertSame('https://github.com/test/issues', $support->issues);
        $this->assertSame('https://docs.test.com', $support->docs);
        $this->assertNull($support->forum);
        $this->assertNull($support->wiki);
    }

    public function testFromStdClassEmpty(): void
    {
        $data = new stdClass();
        $support = Support::fromStdClass($data);

        $this->assertTrue($support->isEmpty());
    }

    public function testIsEmptyWithNoFields(): void
    {
        $support = new Support();
        $this->assertTrue($support->isEmpty());
    }

    public function testIsEmptyWithOneField(): void
    {
        $support = new Support(email: 'test@example.com');
        $this->assertFalse($support->isEmpty());
    }

    public function testSerializeWithAllFields(): void
    {
        $support = new Support(
            email: 'dev@lists.horde.org',
            issues: 'https://github.com/horde/horde/issues',
            docs: 'https://www.horde.org/libraries/Horde_Http',
        );

        $serialized = $support->__serialize();

        $this->assertIsArray($serialized);
        $this->assertCount(3, $serialized);
        $this->assertSame('dev@lists.horde.org', $serialized['email']);
        $this->assertSame('https://github.com/horde/horde/issues', $serialized['issues']);
        $this->assertSame('https://www.horde.org/libraries/Horde_Http', $serialized['docs']);
    }

    public function testSerializeOmitsNullFields(): void
    {
        $support = new Support(
            email: 'test@example.com',
            issues: null,
            docs: 'https://docs.example.com',
        );

        $serialized = $support->__serialize();

        $this->assertArrayHasKey('email', $serialized);
        $this->assertArrayHasKey('docs', $serialized);
        $this->assertArrayNotHasKey('issues', $serialized);
        $this->assertArrayNotHasKey('forum', $serialized);
    }

    public function testToStdClass(): void
    {
        $support = new Support(
            email: 'dev@lists.horde.org',
            issues: 'https://github.com/horde/horde/issues',
        );

        $stdClass = $support->toStdClass();

        $this->assertInstanceOf(stdClass::class, $stdClass);
        $this->assertSame('dev@lists.horde.org', $stdClass->email);
        $this->assertSame('https://github.com/horde/horde/issues', $stdClass->issues);
        $this->assertObjectNotHasProperty('forum', $stdClass);
    }

    public function testUnserialize(): void
    {
        $data = [
            'email' => 'test@example.com',
            'issues' => 'https://github.com/test/issues',
            'docs' => 'https://docs.test.com',
        ];

        $support = new Support();
        $support->__unserialize($data);

        $this->assertSame('test@example.com', $support->email);
        $this->assertSame('https://github.com/test/issues', $support->issues);
        $this->assertSame('https://docs.test.com', $support->docs);
        $this->assertNull($support->forum);
    }

    public function testRoundTripSerializeUnserialize(): void
    {
        $original = new Support(
            email: 'dev@lists.horde.org',
            issues: 'https://github.com/horde/horde/issues',
            docs: 'https://www.horde.org/libraries/Horde_Http',
            source: 'https://github.com/horde/Http',
        );

        $serialized = $original->__serialize();
        $restored = new Support();
        $restored->__unserialize($serialized);

        $this->assertSame($original->email, $restored->email);
        $this->assertSame($original->issues, $restored->issues);
        $this->assertSame($original->docs, $restored->docs);
        $this->assertSame($original->source, $restored->source);
        $this->assertNull($restored->forum);
    }

    public function testModifyFieldsAfterConstruction(): void
    {
        $support = new Support(email: 'old@example.com');
        $support->email = 'new@example.com';
        $support->issues = 'https://github.com/new/issues';

        $this->assertSame('new@example.com', $support->email);
        $this->assertSame('https://github.com/new/issues', $support->issues);
    }

    public function testSetFieldToNullAfterConstruction(): void
    {
        $support = new Support(
            email: 'test@example.com',
            issues: 'https://github.com/test/issues',
        );

        $support->email = null;

        $this->assertNull($support->email);
        $this->assertSame('https://github.com/test/issues', $support->issues);
    }

    public function testIsEmptyAfterNullingAllFields(): void
    {
        $support = new Support(
            email: 'test@example.com',
            issues: 'https://github.com/test/issues',
        );

        $support->email = null;
        $support->issues = null;

        $this->assertTrue($support->isEmpty());
    }

    public function testSerializeAfterModification(): void
    {
        $support = new Support(email: 'original@example.com');
        $support->email = 'modified@example.com';
        $support->issues = 'https://github.com/test/issues';

        $serialized = $support->__serialize();

        $this->assertSame('modified@example.com', $serialized['email']);
        $this->assertSame('https://github.com/test/issues', $serialized['issues']);
    }

    public function testEmptyStringTreatedAsSeparateFromNull(): void
    {
        $support = new Support(email: '');

        $this->assertSame('', $support->email);
        $this->assertFalse($support->isEmpty());
    }

    public function testSerializeIncludesEmptyStringButOmitsNull(): void
    {
        $support = new Support(
            email: '',
            issues: null,
            docs: 'https://docs.test.com',
        );

        $serialized = $support->__serialize();

        $this->assertArrayHasKey('email', $serialized);
        $this->assertSame('', $serialized['email']);
        $this->assertArrayNotHasKey('issues', $serialized);
        $this->assertArrayHasKey('docs', $serialized);
    }

    public function testFromStdClassWithUnexpectedFields(): void
    {
        $data = new stdClass();
        $data->email = 'test@example.com';
        $data->unexpected = 'should-be-ignored';
        $data->issues = 'https://github.com/test/issues';

        $support = Support::fromStdClass($data);

        $this->assertSame('test@example.com', $support->email);
        $this->assertSame('https://github.com/test/issues', $support->issues);
    }

    public function testUnserializeWithMissingFields(): void
    {
        $data = [
            'email' => 'test@example.com',
        ];

        $support = new Support();
        $support->__unserialize($data);

        $this->assertSame('test@example.com', $support->email);
        $this->assertNull($support->issues);
        $this->assertNull($support->docs);
    }

    public function testUnserializeOverwritesExistingValues(): void
    {
        $support = new Support(
            email: 'old@example.com',
            issues: 'https://old-url.com',
        );

        $data = [
            'email' => 'new@example.com',
            'docs' => 'https://new-docs.com',
        ];

        $support->__unserialize($data);

        $this->assertSame('new@example.com', $support->email);
        $this->assertSame('https://new-docs.com', $support->docs);
        $this->assertNull($support->issues);
    }

    public function testUnicodeInFields(): void
    {
        $support = new Support(
            email: 'tëst@éxample.com',
            docs: 'https://docs.test.com/日本語',
        );

        $serialized = $support->__serialize();

        $this->assertSame('tëst@éxample.com', $serialized['email']);
        $this->assertSame('https://docs.test.com/日本語', $serialized['docs']);
    }

    public function testVeryLongUrls(): void
    {
        $longUrl = 'https://example.com/' . str_repeat('very-long-path/', 50);
        $support = new Support(docs: $longUrl);

        $this->assertSame($longUrl, $support->docs);
        $serialized = $support->__serialize();
        $this->assertSame($longUrl, $serialized['docs']);
    }

    public function testSpecialCharactersInUrls(): void
    {
        $support = new Support(
            issues: 'https://github.com/user/repo?label=bug&state=open',
            irc: 'ircs://irc.libera.chat:6697/#horde',
            rss: 'https://example.com/feed.xml?format=rss&category=news',
        );

        $this->assertSame('https://github.com/user/repo?label=bug&state=open', $support->issues);
        $this->assertSame('ircs://irc.libera.chat:6697/#horde', $support->irc);
        $this->assertSame('https://example.com/feed.xml?format=rss&category=news', $support->rss);
    }

    public function testFromStdClassPreservesAllFieldTypes(): void
    {
        $data = new stdClass();
        $data->email = '';
        $data->issues = 'https://github.com/test/issues';
        $data->forum = null;

        $support = Support::fromStdClass($data);

        $this->assertSame('', $support->email);
        $this->assertSame('https://github.com/test/issues', $support->issues);
        $this->assertNull($support->forum);
    }
}
