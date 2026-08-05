<?php

namespace DeptOfScrapyardRobotics\Actuators\SeesawNeoSlider;

use RuntimeException;

class SeesawNeoSliderException extends RuntimeException
{
    public static function i2cReadFailed(int $expected_bytes): static
    {
        return new static("Seesaw I2C read failed; expected {$expected_bytes} bytes.");
    }

    public static function i2cWriteFailed(int $expected_bytes, int $written_bytes): static
    {
        return new static("Seesaw I2C write failed; expected {$expected_bytes} bytes, wrote {$written_bytes}.");
    }

    public static function unexpectedProduct(int $expected, int $actual): static
    {
        return new static("Expected seesaw product PID {$expected}, received {$actual}.");
    }

    public static function invalidPixel(int $pixel, int $pixel_count): static
    {
        return new static("NeoPixel index {$pixel} is outside the available range 0-".($pixel_count - 1).'.');
    }

    public static function invalidColorArguments(): static
    {
        return new static('Provide either one packed 0xRRGGBB color or separate red, green, and blue values.');
    }

    public static function unsupportedWhiteChannel(): static
    {
        return new static('The seesaw NeoSlider contains RGB NeoPixels and does not support a white channel.');
    }

    public static function invalidColorChannel(int $value): static
    {
        return new static("NeoPixel color channel {$value} is outside the range 0-255.");
    }

    public static function invalidBrightness(float $brightness): static
    {
        return new static("NeoPixel brightness {$brightness} is outside the range 0.0-1.0.");
    }
}
