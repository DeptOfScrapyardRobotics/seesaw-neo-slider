# Agent guidelines — dept-of-scrapyard-robotics/seesaw-neo-slider

## Knowledge Bundle (OKF)

This package ships an Open Knowledge Format bundle at [`.okf/`](.okf/) (excluded from Composer dist via `.gitattributes` `export-ignore`).

Before changing this package or advising on NeoSlider architecture:

1. Read [`.okf/index.md`](.okf/index.md) first (progressive disclosure).
2. Open only the linked concepts needed for the task.
3. Prefer `status: stable`. Treat `deprecated` as historical only. New agent-written concepts stay `status: draft` until a human verifies them.
4. When you learn something durable about **this package**, update the affected `.okf` concept(s) and append `.okf/log.md`.
5. Keep the `.okf` bundle at the **package root** only — do not nest extra `.okf` folders under `src/`.
6. Circuits registry semantics belong in `scrapyard-io/gpio-framework`’s `.okf`. Actuation contracts/wrappers belong in `scrapyard-io/waveforms`.

## Package rules (quick) — 0.7.x

- Composer: `dept-of-scrapyard-robotics/seesaw-neo-slider` **0.7.0**. Namespace `DeptOfScrapyardRobotics\Actuators\SeesawNeoSlider\`.
- Requires leaf components (not kitchen-sink frameworks): `fabricate/nuts-and-bolts`, `gpio/circuits`, `gpio/contracts`, `gpio/i2c`, `waveforms/contracts`.
- Provider: `SeesawNeoSliderServiceProvider` at package root. Catalog slug `seesaw-neo-slider`. Command `seesaw-neo-slider:make-profile`.
- IC extends `Actuator` (gpio Types), implements `BootSequence` + Waveforms `Potentiometer` + `LEDStrip`; factories `i2c(...)` / `fromI2CBus(...)`.
- No package-local Actuator wrapper — compose `Waveforms\Potentiometers\Potentiometer` / `Waveforms\LEDs\NeoPixel`.
- GeneralPurposeIO Circuit attributes only. Enums string/int-backed, FULLY UPPERCASE cases; no class constants; prefer `is_null`.
- I2C addr **0x30**; PID **5295**; ADC pin **18** (0..1023); NeoPixel pin **14**, **4** RGB pixels.
