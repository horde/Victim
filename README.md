# horde/victim

Read and write Composer.json files and iterate composer directory structures without composer or framework dependencies.

## Usage

Provides value objects for `composer.json` documents, vendor directory iteration, and Packagist-style support metadata under the `Horde\Victim\` namespace.

## Compatibility

Currently targets composer 2.2 format for writing but should be compatible down to composer 2.0 and mostly compatible with composer 1.x, except pear repository types.

## Origin

Forked from `horde/composer`. Same or similar implementations have existed in
- horde/horde-installer-plugin Composer plugin
- horde/components Developer CLI
- horde/hordectl Admin CLI

## Tools

A bundled `ci-tools/horde-components.phar` is included for working with this repository using the Horde components developer CLI. The directory is named `ci-tools/` rather than `tools/` so that common `.gitignore` snippets which exclude `tools/` do not strip it from the working tree.
