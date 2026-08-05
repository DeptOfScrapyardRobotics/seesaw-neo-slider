<?php

namespace DeptOfScrapyardRobotics\Actuators\SeesawNeoSlider\Providers;

use DeptOfScrapyardRobotics\Actuators\SeesawNeoSlider\NeopixelSlider;
use DeptOfScrapyardRobotics\Actuators\SeesawNeoSlider\SeesawNeoSlider;
use Fabricate\NutsAndBolts\MagicAliases\Actuator;
use Fabricate\NutsAndBolts\MagicAliases\Circuit;
use Fabricate\NutsAndBolts\ServiceProvider;

class SeesawNeoSliderServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Circuit::addCircuit('seesaw-neo-slider', SeesawNeoSlider::class);
        Actuator::addActuator('neopixel-slider', NeopixelSlider::class);
    }
}
