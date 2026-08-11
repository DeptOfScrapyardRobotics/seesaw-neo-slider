---
type: Module
title: Seesaw NeoSlider IC
description: Actuator IC for Adafruit PID 5295 — potentiometer + four-pixel NeoPixel strip over Seesaw I2C.
resource: src/SeesawNeoSlider.php
tags: [seesaw, neo-slider, potentiometer, neopixel, waveforms]
generated: { by: cursor-agent/grok-4.5, at: "2026-08-11T20:55:00Z" }
verified: { by: null, at: null }
status: draft
sources:
  - id: ic
    resource: src/SeesawNeoSlider.php
    title: SeesawNeoSlider
  - id: client
    resource: src/SeesawClient.php
    title: SeesawClient
  - id: spec
    resource: src/Enums/SliderSpecification.php
    title: SliderSpecification
---

# Role

`SeesawNeoSlider` extends `GeneralPurposeIO\Circuits\Types\Actuator`, implements `BootSequence`, `Waveforms\Contracts\Actuation\Interfaces\Potentiometer`, and `LEDStrip`. Attributes are GeneralPurposeIO Circuits (`#[IntegratedCircuit('I2C')]`, `#[Pinout(...)]`).[^ic]

# Identity / bus

| Item | Value |
|------|-------|
| I2C address | `SliderSpecification::DEFAULT_ADDRESS` = **0x30** |
| Product PID | status version bits 31:16 = **5295** |
| ADC | module `0x09`, pin **18**, 10-bit **0..1023** |
| NeoPixels | module `0x0E`, pin **14**, **4** RGB pixels (GRB buffer) |

Boot confirms PID, configures the NeoPixel module, clears, and shows.[^spec][^client]

# Waveforms usage

```php
use GeneralPurposeIO\Core\MagicAliases\Circuit;
use Waveforms\LEDs\NeoPixel;
use Waveforms\Potentiometers\Potentiometer;

$ic = Circuit::profile('neoslider_board'); // recipe ic => seesaw-neo-slider

$pot = new Potentiometer($ic);
$leds = new NeoPixel($ic);

$position = $pot->position(); // 0.0..1.0
$leds->fill(0xFF0000)->show();
```

# Factories

```php
SeesawNeoSlider::i2c($device, adapter: null, slave: 0x30, boot_now: true);
SeesawNeoSlider::fromI2CBus($i2cSlave, boot_now: true);
```

# Related

* [Package (0.7)](../orientation/package.md)
* [Circuits integration](circuits.md)

[^ic]: SeesawNeoSlider
[^client]: SeesawClient
[^spec]: SliderSpecification
