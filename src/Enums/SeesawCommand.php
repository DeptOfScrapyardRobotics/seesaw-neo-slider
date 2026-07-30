<?php

namespace DeptOfScrapyardRobotics\Actuators\SeesawNeoSlider\Enums;

enum SeesawCommand: string
{
    case STATUS_HARDWARE_ID = 'status.hardware_id';
    case STATUS_VERSION = 'status.version';
    case ADC_CHANNEL = 'adc.channel';
    case NEOPIXEL_PIN = 'neopixel.pin';
    case NEOPIXEL_BUFFER_LENGTH = 'neopixel.buffer_length';
    case NEOPIXEL_BUFFER = 'neopixel.buffer';
    case NEOPIXEL_SHOW = 'neopixel.show';

    public function module(): int
    {
        return match ($this) {
            self::STATUS_HARDWARE_ID, self::STATUS_VERSION => 0x00,
            self::ADC_CHANNEL => 0x09,
            self::NEOPIXEL_PIN,
            self::NEOPIXEL_BUFFER_LENGTH,
            self::NEOPIXEL_BUFFER,
            self::NEOPIXEL_SHOW => 0x0E,
        };
    }

    public function register(int $channel = 0): int
    {
        return match ($this) {
            self::STATUS_HARDWARE_ID => 0x01,
            self::STATUS_VERSION => 0x02,
            self::ADC_CHANNEL => 0x07 + $channel,
            self::NEOPIXEL_PIN => 0x01,
            self::NEOPIXEL_BUFFER_LENGTH => 0x03,
            self::NEOPIXEL_BUFFER => 0x04,
            self::NEOPIXEL_SHOW => 0x05,
        };
    }
}
