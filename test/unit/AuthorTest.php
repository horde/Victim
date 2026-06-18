<?php

declare(strict_types=1);

namespace Horde\Victim\Test\Unit;

use Horde\Victim\Author;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use InvalidArgumentException;

#[CoversClass(Author::class)]
class AuthorTest extends TestCase
{
    public function testConstructorWithNameOnly(): void
    {
        $author = new Author('John Doe');

        $this->assertSame('John Doe', $author->name);
        $this->assertSame('', $author->email);
        $this->assertSame('', $author->homepage);
        $this->assertSame('', $author->role);
    }

    public function testConstructorWithAllParameters(): void
    {
        $author = new Author(
            name: 'Jane Smith',
            email: 'jane@example.com',
            homepage: 'https://example.com',
            role: 'lead'
        );

        $this->assertSame('Jane Smith', $author->name);
        $this->assertSame('jane@example.com', $author->email);
        $this->assertSame('https://example.com', $author->homepage);
        $this->assertSame('lead', $author->role);
    }

    public function testConstructorThrowsExceptionForEmptyName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Name cannot be empty');

        new Author('');
    }

    public function testSerialize(): void
    {
        $author = new Author(
            name: 'Bob Johnson',
            email: 'bob@example.com',
            homepage: 'https://bob.example.com',
            role: 'contributor'
        );

        $serialized = $author->__serialize();

        $this->assertIsArray($serialized);
        $this->assertArrayHasKey('name', $serialized);
        $this->assertArrayHasKey('email', $serialized);
        $this->assertArrayHasKey('homepage', $serialized);
        $this->assertArrayHasKey('role', $serialized);

        $this->assertSame('Bob Johnson', $serialized['name']);
        $this->assertSame('bob@example.com', $serialized['email']);
        $this->assertSame('https://bob.example.com', $serialized['homepage']);
        $this->assertSame('contributor', $serialized['role']);
    }

    public function testToStringReturnsJsonRepresentation(): void
    {
        $author = new Author(
            name: 'Alice Cooper',
            email: 'alice@example.com',
            role: 'maintainer'
        );

        $string = (string) $author;

        $this->assertJson($string);

        $decoded = json_decode($string, true);
        $this->assertSame('Alice Cooper', $decoded['name']);
        $this->assertSame('alice@example.com', $decoded['email']);
        $this->assertSame('maintainer', $decoded['role']);
    }

    public function testToStringIncludesEmptyFields(): void
    {
        $author = new Author('Test Author');

        $string = (string) $author;
        $decoded = json_decode($string, true);

        $this->assertArrayHasKey('email', $decoded);
        $this->assertArrayHasKey('homepage', $decoded);
        $this->assertArrayHasKey('role', $decoded);
        $this->assertSame('', $decoded['email']);
        $this->assertSame('', $decoded['homepage']);
        $this->assertSame('', $decoded['role']);
    }

    public function testModifyingEmailAfterConstruction(): void
    {
        $author = new Author('Test User');
        $author->email = 'updated@example.com';

        $this->assertSame('updated@example.com', $author->email);
    }

    public function testModifyingRoleAfterConstruction(): void
    {
        $author = new Author('Test User', role: 'contributor');
        $author->role = 'lead';

        $this->assertSame('lead', $author->role);
    }

    public function testModifyingHomepageAfterConstruction(): void
    {
        $author = new Author('Test User');
        $author->homepage = 'https://newsite.example.com';

        $this->assertSame('https://newsite.example.com', $author->homepage);
    }
}
