---
type: Module
title: Circuits integration
description: Catalog registration, seesaw-neo-slider:make-profile, and profiles.
resource: src/SeesawNeoSliderServiceProvider.php
tags: [circuits, catalog, profile]
generated: { by: cursor-agent/grok-4.5, at: "2026-08-11T20:55:00Z" }
verified: { by: null, at: null }
status: draft
sources:
  - id: provider
    resource: src/SeesawNeoSliderServiceProvider.php
    title: SeesawNeoSliderServiceProvider
  - id: catalog
    resource: src/Enums/SeesawNeoSliderCatalogIc.php
    title: SeesawNeoSliderCatalogIc
  - id: console-enum
    resource: src/Enums/SeesawNeoSliderConsoleCommand.php
    title: SeesawNeoSliderConsoleCommand
  - id: make-profile
    resource: src/Console/SeesawNeoSliderMakeProfileCommand.php
    title: seesaw-neo-slider:make-profile
---

# Role

This package owns the NeoSlider chip driver and registers it with gpio-framework Circuits. Registry / fluent / profile semantics live in `scrapyard-io/gpio-framework`.

# Catalog

On `boot()`:[^provider]

```php
Circuit::addCircuit(SeesawNeoSliderCatalogIc::SEESAW_NEO_SLIDER->value, SeesawNeoSlider::class); // 'seesaw-neo-slider'

$maker = SeesawNeoSliderConsoleCommand::MAKE_PROFILE->value; // 'seesaw-neo-slider:make-profile'
foreach (SeesawNeoSliderCatalogIc::cases() as $ic) {
    Circuit::registerProfileCommand($ic->value, $maker);
}
```

Slug enum: `SeesawNeoSliderCatalogIc::SEESAW_NEO_SLIDER` → `seesaw-neo-slider`.[^catalog][^console-enum]

# Profiles

```bash
workshop vendor:publish --tag=gpio-circuits-config
workshop circuit:make-profile
workshop seesaw-neo-slider:make-profile
```

`seesaw-neo-slider:make-profile` uses `ScaffoldsCircuitProfiles` + `CircuitAttributeInspector` — prompts from `#[IntegratedCircuit]` / `#[Pinout]`, writes `config/circuits.php` with `boot_now => true`.[^make-profile]

```php
use GeneralPurposeIO\Core\MagicAliases\Circuit;

/** @var \DeptOfScrapyardRobotics\Actuators\SeesawNeoSlider\SeesawNeoSlider $slider */
$slider = Circuit::profile('neoslider_board');

$raw = $slider->raw();
$pos = $slider->position();
```

Example `config/circuits.php` recipe shape:

```php
'neoslider_board' => [
    'ic' => 'seesaw-neo-slider',
    'protocol' => 'i2c',
    'driver' => 'posix',
    'device' => '/dev/i2c-1',
    'slave' => 0x30,
    'boot_now' => true,
],
```

# Related

* [Seesaw NeoSlider IC](seesaw-neo-slider.md)
* [Package (0.7)](../orientation/package.md)

[^provider]: SeesawNeoSliderServiceProvider
[^catalog]: SeesawNeoSliderCatalogIc
[^console-enum]: SeesawNeoSliderConsoleCommand
[^make-profile]: seesaw-neo-slider:make-profile
