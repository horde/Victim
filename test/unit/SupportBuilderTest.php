<?php

declare(strict_types=1);

namespace Horde\Victim\Test\Unit;

use Horde\Victim\Support;
use Horde\Victim\SupportBuilder;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(SupportBuilder::class)]
class SupportBuilderTest extends TestCase
{
    public function testBuildWithAllFields(): void
    {
        $support = (new SupportBuilder())
            ->email('dev@lists.horde.org')
            ->issues('https://github.com/horde/horde/issues')
            ->forum('https://forum.horde.org')
            ->wiki('https://wiki.horde.org')
            ->chat('https://gitter.im/horde/community')
            ->docs('https://www.horde.org/libraries/Horde_Http')
            ->source('https://github.com/horde/Http')
            ->irc('irc://irc.freenode.net/horde')
            ->rss('https://www.horde.org/feed.xml')
            ->security('https://www.horde.org/security')
            ->build();

        $this->assertInstanceOf(Support::class, $support);
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

    public function testBuildWithPartialFields(): void
    {
        $support = (new SupportBuilder())
            ->email('test@example.com')
            ->issues('https://github.com/test/issues')
            ->build();

        $this->assertSame('test@example.com', $support->email);
        $this->assertSame('https://github.com/test/issues', $support->issues);
        $this->assertNull($support->docs);
        $this->assertNull($support->forum);
    }

    public function testBuildEmpty(): void
    {
        $support = (new SupportBuilder())->build();

        $this->assertTrue($support->isEmpty());
    }

    public function testMethodChaining(): void
    {
        $builder = new SupportBuilder();
        $result = $builder->email('test@example.com');

        $this->assertSame($builder, $result);
    }

    public function testIndividualFieldMethods(): void
    {
        $builder = new SupportBuilder();

        $builder->email('email@test.com');
        $builder->issues('https://issues.test.com');
        $builder->forum('https://forum.test.com');
        $builder->wiki('https://wiki.test.com');
        $builder->chat('https://chat.test.com');
        $builder->docs('https://docs.test.com');
        $builder->source('https://source.test.com');
        $builder->irc('irc://irc.test.com/channel');
        $builder->rss('https://rss.test.com');
        $builder->security('https://security.test.com');

        $support = $builder->build();

        $this->assertSame('email@test.com', $support->email);
        $this->assertSame('https://issues.test.com', $support->issues);
        $this->assertSame('https://forum.test.com', $support->forum);
        $this->assertSame('https://wiki.test.com', $support->wiki);
        $this->assertSame('https://chat.test.com', $support->chat);
        $this->assertSame('https://docs.test.com', $support->docs);
        $this->assertSame('https://source.test.com', $support->source);
        $this->assertSame('irc://irc.test.com/channel', $support->irc);
        $this->assertSame('https://rss.test.com', $support->rss);
        $this->assertSame('https://security.test.com', $support->security);
    }

    public function testCallingMethodTwiceOverwritesValue(): void
    {
        $builder = new SupportBuilder();

        $builder->email('first@example.com');
        $builder->email('second@example.com');

        $support = $builder->build();

        $this->assertSame('second@example.com', $support->email);
    }

    public function testBuilderCanBeReusedForMultipleBuilds(): void
    {
        $builder = new SupportBuilder();
        $builder->email('test@example.com');
        $builder->issues('https://github.com/test/issues');

        $first = $builder->build();
        $second = $builder->build();

        $this->assertSame('test@example.com', $first->email);
        $this->assertSame('test@example.com', $second->email);
        $this->assertNotSame($first, $second);
    }

    public function testBuilderModificationAfterBuildDoesNotAffectPreviousInstances(): void
    {
        $builder = new SupportBuilder();
        $builder->email('first@example.com');

        $first = $builder->build();

        $builder->email('second@example.com');
        $second = $builder->build();

        $this->assertSame('first@example.com', $first->email);
        $this->assertSame('second@example.com', $second->email);
    }

    public function testBuilderAcceptsEmptyStrings(): void
    {
        $builder = new SupportBuilder();
        $support = $builder->email('')->build();

        $this->assertSame('', $support->email);
        $this->assertFalse($support->isEmpty());
    }
}
