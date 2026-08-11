# Directory Update Log

## 2026-08-11

* **Fix (draft)**: Composer `require` uses leaf components (`gpio/*`, `waveforms/contracts` or `tubes/contracts`, `fabricate/nuts-and-bolts`) — no `scrapyard-io/gpio-framework` / `scrapyard-io/waveforms` / `scrapyard-io/tubes` kitchen sinks. Amended [package](orientation/package.md).

* **0.7 rewire**: Dropped Fabricate 0.6 Actuation/`NeopixelSlider` wrapper; IC implements Waveforms `Potentiometer` + `LEDStrip` + GPIO `BootSequence`; Circuits via `GeneralPurposeIO\Core\MagicAliases\Circuit`; catalog `seesaw-neo-slider`; `seesaw-neo-slider:make-profile`; exception extends Waveforms `ActuatorException`; deps `gpio-framework` + `waveforms` ^0.7.
