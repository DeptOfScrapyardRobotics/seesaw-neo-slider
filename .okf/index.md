---
okf_version: "0.2"
---

# dept-of-scrapyard-robotics/seesaw-neo-slider Knowledge Bundle

Package knowledge for `dept-of-scrapyard-robotics/seesaw-neo-slider` (Adafruit PID 5295 Seesaw NeoSlider, v0.7.x).
Read this index first; open only the concepts needed for the task.

**Trust rule:** Prefer `status: stable`. Treat `deprecated` as historical only. New agent-written concepts stay `status: draft` until a human verifies them.
**Placement:** Package-root `.okf/` only — never under `src/`.
**Links:** Concept cross-links use paths relative to each file.
**Scope:** This package’s IC surface, Circuits catalog registration, and profiles. Registry semantics live in `scrapyard-io/gpio-framework`. Actuation contracts `Potentiometer` / `LEDStrip` and wrappers live in `scrapyard-io/waveforms`.
**Dist note:** `.okf/` and root `AGENTS.md` are `export-ignore` in `.gitattributes`.

# Orientation

* [Package (0.7)](orientation/package.md) - Composer identity, namespace, provider, dependencies.

# Core

* [Seesaw NeoSlider IC](core/seesaw-neo-slider.md) - Actuator IC, PID 5295, potentiometer + NeoPixel strip, BootSequence.
* [Circuits integration](core/circuits.md) - Catalog slug, make-profile, profiles.

# Log

* [Directory update log](log.md)
