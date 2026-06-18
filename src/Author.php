<?php

declare(strict_types=1);

namespace Horde\Victim;

use Stringable;

class Author implements Stringable
{
    public function __construct(public readonly string $name, public string $email = '', public string $homepage = '', public string $role = '')
    {
        if (empty($name)) {
            throw new \InvalidArgumentException('Name cannot be empty');
        }
    }

    public function __toString(): string
    {
        return json_encode($this->__serialize(), JSON_PRETTY_PRINT);
    }

    public function __serialize(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'homepage' => $this->homepage,
            'role' => $this->role,
        ];
    }
    public function __unserialize(array $data): void
    {
        $this->name = $data['name'];
        $this->email = $data['email'];
        $this->homepage = $data['homepage'];
        $this->role = $data['role'];
    }
}
