---
type: Module
title: Package (0.7)
description: dept-of-scrapyard-robotics/seesaw-neo-slider Composer identity, namespace, and discovery.
resource: composer.json
tags: [orientation, package, 0.7, seesaw-neo-slider]
generated: { by: cursor-agent/grok-4.5, at: "2026-08-11T20:55:00Z" }
verified: { by: null, at: null }
status: draft
sources:
  - id: composer
    resource: composer.json
    title: Package composer.json
  - id: provider
    resource: src/SeesawNeoSliderServiceProvider.php
    title: SeesawNeoSliderServiceProvider
  - id: gitattributes
    resource: .gitattributes
    title: Dist export-ignore
---

# Identity

| Field | Value |
|-------|-------|
| Composer | `dept-of-scrapyard-robotics/seesaw-neo-slider` **0.7.0** |
| PHP | `^8.4\|^8.5\|^8.6` |
| Namespace | `DeptOfScrapyardRobotics\Actuators\SeesawNeoSlider\` → `src/` |
| Provider | `DeptOfScrapyardRobotics\Actuators\SeesawNeoSlider\SeesawNeoSliderServiceProvider` (package root, not `Providers/`) |
| Catalog slug | `seesaw-neo-slider` |

# Requires

| Package | Constraint |
|---------|------------|
| `fabricate/nuts-and-bolts` | `^0.7.0` |
| `gpio/circuits` | `^0.7.0` |
| `gpio/contracts` | `^0.7.0` |
| `gpio/i2c` | `^0.7.0` |
| `waveforms/contracts` | `^0.7.0` |

Suggested (optional): `microscrap/i2c`, `microscrap/mpsse` at `^0.7.0`.[^composer]

# Discovery

`extra.scrapyard-io.providers` lists `SeesawNeoSliderServiceProvider`. That provider registers the catalog IC and wires `seesaw-neo-slider:make-profile` into `circuit:make-profile`.[^provider]

No package-local Actuator wrapper — use `Waveforms\Potentiometers\Potentiometer` and/or `Waveforms\LEDs\NeoPixel` over `Circuit::profile(...)`.

# Dist

`.okf/` and `AGENTS.md` are `export-ignore` — Composer dist tarballs omit them.[^gitattributes]

# Related

* [Seesaw NeoSlider IC](../core/seesaw-neo-slider.md)
* [Circuits integration](../core/circuits.md)

[^composer]: Package composer.json
[^provider]: SeesawNeoSliderServiceProvider
[^gitattributes]: Dist export-ignore
