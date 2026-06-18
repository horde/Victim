<?php

declare(strict_types=1);

namespace Horde\Victim;

use InvalidArgumentException;
use RuntimeException;
use stdClass;
use Stringable;
use JsonException;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Exception;

class ComposerJsonFile implements Stringable
{
    private stdClass $composerJson;
    public function __construct(
        private string $filePath,
        string|Stringable|null $json = null
    ) {
        if ($json !== null) {
            $content = (string) $json;
        } else {
            if (!file_exists($this->filePath)) {
                throw new InvalidComposerJsonFileException("File does not exist: {$this->filePath}");
            }
            if (!is_readable($this->filePath)) {
                throw new InvalidComposerJsonFileException("File is not readable: {$this->filePath}");
            }
            $content = file_get_contents($this->filePath);
        }
        if (!json_validate($content)) {
            throw new InvalidComposerJsonFileException("Invalid JSON in file: {$this->filePath}");
        }
        $this->composerJson = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
    }

    public function __toString(): string
    {
        $json = json_encode($this->composerJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        if ($json === false) {
            throw new JsonException("Failed to encode JSON: " . json_last_error_msg());
        }
        return $json;
    }

    public function save(): void
    {
        if (file_put_contents($this->filePath, $this) === false) {
            throw new RuntimeException("Failed to write to file: {$this->filePath}");
        }
    }

    public function getName(bool $failIfMissing = false): string
    {
        if ($failIfMissing && !isset($this->composerJson->name)) {
            throw new RuntimeException("Package name not found in composer.json");
        }
        return $this->composerJson->name ?? '';
    }
    public function setPreferStable(bool $preferStable = true): self
    {
        $this->composerJson->{'prefer-stable'} = $preferStable;
        return $this;
    }

    public function setMinimumStability(string $stability = 'stable'): self
    {
        if (!in_array($stability, ['dev', 'alpha', 'beta', 'RC', 'stable'])) {
            throw new RuntimeException('Invalid stability level: ' . $stability);
        }
        $this->composerJson->{'minimum-stability'} = $stability;
        return $this;
    }

    public function setName(string|Stringable $name): self
    {
        if (mb_strpos((string) $name, '/') === false) {
            throw new InvalidArgumentException("Invalid name: {$name}");
        }
        $this->composerJson->name = (string) $name;
        return $this;
    }

    public function getType(): string
    {
        return $this->composerJson->type ?? '';
    }

    public function setType(string $type): self
    {
        $this->composerJson->type = $type;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->composerJson->description ?? '';
    }
    public function setDescription(string $description): self
    {
        $this->composerJson->description = $description;
        return $this;
    }

    public function getLicense(): string
    {
        return $this->composerJson->license ?? '';
    }
    public function setLicense(string $license): self
    {
        $this->composerJson->license = $license;
        return $this;
    }
    public function getHomepage(): string
    {
        return $this->composerJson->homepage ?? '';
    }
    public function setHomepage(string $homepage): self
    {
        $this->composerJson->homepage = $homepage;
        return $this;
    }

    public function getTime(): DateTimeImmutable
    {
        if (!isset($this->composerJson->time)) {
            throw new RuntimeException("Time not found in composer.json");
        }
        try {
            // https://getcomposer.org/doc/04-schema.md#time By definition composer.json time is in UTC
            return new DateTimeImmutable($this->composerJson->time, new DateTimeZone('UTC'));
        } catch (Exception $e) {
            throw new RuntimeException("Invalid time format in composer.json: " . $e->getMessage());
        }
    }
    public function setTime(DateTimeInterface $time): self
    {
        $this->composerJson->time = $time->format('Y-m-d H:i:s');
        return $this;
    }

    public function addBranchAlias(string $alias, string $branch): self
    {
        if (!isset($this->composerJson->{'extra'}->{"branch-alias"})) {
            $this->composerJson->{'extra'}->{"branch-alias"} = new stdClass();
        }
        $this->composerJson->{'extra'}->{"branch-alias"}->{$branch} = $alias;
        return $this;
    }
    public function getBranchAlias(string $branch): ?string
    {
        if (isset($this->composerJson->{'extra'}->{"branch-alias"}->{$branch})) {
            return $this->composerJson->{'extra'}->{"branch-alias"}->{$branch};
        }
        return null;
    }
    public function getBranchAliases(): array
    {
        if (isset($this->composerJson->{'extra'}->{"branch-alias"})) {
            return (array) $this->composerJson->{'extra'}->{"branch-alias"};
        }
        return [];
    }
    public function removeBranchAlias(string $branch): self
    {
        if (isset($this->composerJson->{'extra'}->{"branch-alias"}->{$branch})) {
            unset($this->composerJson->{'extra'}->{"branch-alias"}->{$branch});
        }
        return $this;
    }

    /**
     * @return array<Author>
     */
    public function getAuthors(): array
    {
        $authors = [];
        foreach ($this->composerJson->authors as $authorPlain) {
            $authors[] = new Author(
                $authorPlain->name ?? '',
                $authorPlain->email ?? '',
                $authorPlain->homepage ?? '',
                $authorPlain->role ?? ''
            );
        }
        return $authors;
    }

    public function setAuthors(array $authors): self
    {
        foreach ($authors as $author) {
            if ($author instanceof Author) {

            }
        }
        $this->composerJson->authors = $authors;
        return $this;
    }
    public function hasAuthor(string $name): bool
    {
        foreach ($this->getAuthors() as $author) {
            if (isset($author->name) && $author->name === $name) {
                return true;
            }
        }
        return false;
    }

    public function addAuthor(Author $author): self
    {
        if ($this->hasAuthor($author->name)) {
            throw new RuntimeException("Author already exists: {$author->name}");
        }
        $this->composerJson->authors[] = $author;
        return $this;
    }

    public function getSupport(): ?Support
    {
        if (!isset($this->composerJson->support)) {
            return null;
        }
        return Support::fromStdClass($this->composerJson->support);
    }

    public function setSupport(Support $support): self
    {
        if ($support->isEmpty()) {
            unset($this->composerJson->support);
        } else {
            $this->composerJson->support = $support->toStdClass();
        }
        return $this;
    }
}
