<?php

namespace DeptOfScrapyardRobotics\Actuators\SeesawNeoSlider;

use Fabricate\Actuation\Actuator;
use Fabricate\Contracts\Actuation\ActuatorException;
use Fabricate\NutsAndBolts\MagicAliases\Circuit;

class NeopixelSlider extends Actuator
{
    public function __construct(SeesawNeoSlider $circuit)
    {
        parent::__construct($circuit);
    }

    public function raw(): int
    {
        return $this->slider()->raw();
    }

    public function position(): float
    {
        return $this->slider()->position();
    }

    public function pixelCount(): int
    {
        return $this->slider()->pixelCount();
    }

    public function length(): int
    {
        return $this->slider()->length();
    }

    public function setPixelColor(
        int $pixel,
        int $color_or_red,
        ?int $green = null,
        ?int $blue = null,
        ?int $white = null,
    ): static {
        $this->slider()->setPixelColor($pixel, $color_or_red, $green, $blue, $white);

        return $this;
    }

    public function getPixelColor(int $pixel): int
    {
        return $this->slider()->getPixelColor($pixel);
    }

    public function fill(
        int $color_or_red,
        ?int $green = null,
        ?int $blue = null,
        ?int $white = null,
    ): static {
        $this->slider()->fill($color_or_red, $green, $blue, $white);

        return $this;
    }

    public function clear(): static
    {
        $this->slider()->clear();

        return $this;
    }

    public function brightness(float $brightness): static
    {
        $this->slider()->setBrightness($brightness);

        return $this;
    }

    public function show(): static
    {
        $this->slider()->show();

        return $this;
    }

    public function chase(
        int $color,
        int $cycles = 1,
        int $delay_us = 50_000,
    ): static {
        for ($cycle = 0; $cycle < $cycles; $cycle++) {
            for ($pixel = 0; $pixel < $this->pixelCount(); $pixel++) {
                $this->slider()
                    ->clear()
                    ->setPixelColor($pixel, $color)
                    ->show();

                if ($delay_us > 0) {
                    usleep($delay_us);
                }
            }
        }

        return $this;
    }

    public function close(): void
    {
        $this->slider()->close();
    }

    public static function circuit(string $driver): static
    {
        $circuit = Circuit::driver($driver);

        if (! $circuit instanceof SeesawNeoSlider) {
            $circuit->close();

            throw new ActuatorException("Circuit [{$driver}] is not a SeesawNeoSlider.");
        }

        return new static($circuit);
    }

    protected function slider(): SeesawNeoSlider
    {
        /** @var SeesawNeoSlider */
        return $this->circuit;
    }
}
