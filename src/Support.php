<?php

declare(strict_types=1);

namespace Horde\Victim;

use stdClass;

/**
 * Represents the support section of composer.json
 *
 * Provides contact and resource information for package users.
 *
 * Copyright 2013-2026 The Horde Project (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 */
class Support
{
    public function __construct(
        public ?string $email = null,
        public ?string $issues = null,
        public ?string $forum = null,
        public ?string $wiki = null,
        public ?string $chat = null,
        public ?string $docs = null,
        public ?string $source = null,
        public ?string $irc = null,
        public ?string $rss = null,
        public ?string $security = null,
    ) {}

    /**
     * Create from stdClass representation
     */
    public static function fromStdClass(stdClass $data): self
    {
        return new self(
            email: $data->email ?? null,
            issues: $data->issues ?? null,
            forum: $data->forum ?? null,
            wiki: $data->wiki ?? null,
            chat: $data->chat ?? null,
            docs: $data->docs ?? null,
            source: $data->source ?? null,
            irc: $data->irc ?? null,
            rss: $data->rss ?? null,
            security: $data->security ?? null,
        );
    }

    /**
     * Check if support section has any values
     */
    public function isEmpty(): bool
    {
        return $this->email === null
            && $this->issues === null
            && $this->forum === null
            && $this->wiki === null
            && $this->chat === null
            && $this->docs === null
            && $this->source === null
            && $this->irc === null
            && $this->rss === null
            && $this->security === null;
    }

    /**
     * Serialize to array for JSON encoding
     *
     * Only includes non-null fields (composer.json convention)
     */
    public function __serialize(): array
    {
        $result = [];
        if ($this->email !== null) {
            $result['email'] = $this->email;
        }
        if ($this->issues !== null) {
            $result['issues'] = $this->issues;
        }
        if ($this->forum !== null) {
            $result['forum'] = $this->forum;
        }
        if ($this->wiki !== null) {
            $result['wiki'] = $this->wiki;
        }
        if ($this->chat !== null) {
            $result['chat'] = $this->chat;
        }
        if ($this->docs !== null) {
            $result['docs'] = $this->docs;
        }
        if ($this->source !== null) {
            $result['source'] = $this->source;
        }
        if ($this->irc !== null) {
            $result['irc'] = $this->irc;
        }
        if ($this->rss !== null) {
            $result['rss'] = $this->rss;
        }
        if ($this->security !== null) {
            $result['security'] = $this->security;
        }
        return $result;
    }

    public function __unserialize(array $data): void
    {
        $this->email = $data['email'] ?? null;
        $this->issues = $data['issues'] ?? null;
        $this->forum = $data['forum'] ?? null;
        $this->wiki = $data['wiki'] ?? null;
        $this->chat = $data['chat'] ?? null;
        $this->docs = $data['docs'] ?? null;
        $this->source = $data['source'] ?? null;
        $this->irc = $data['irc'] ?? null;
        $this->rss = $data['rss'] ?? null;
        $this->security = $data['security'] ?? null;
    }

    /**
     * Convert to stdClass for JSON encoding
     */
    public function toStdClass(): stdClass
    {
        return (object) $this->__serialize();
    }
}
