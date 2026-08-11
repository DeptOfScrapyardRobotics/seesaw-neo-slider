<?php

namespace DeptOfScrapyardRobotics\Actuators\SeesawNeoSlider;

use DeptOfScrapyardRobotics\Actuators\SeesawNeoSlider\Console\SeesawNeoSliderMakeProfileCommand;
use DeptOfScrapyardRobotics\Actuators\SeesawNeoSlider\Enums\SeesawNeoSliderCatalogIc;
use DeptOfScrapyardRobotics\Actuators\SeesawNeoSlider\Enums\SeesawNeoSliderConsoleCommand;
use Fabricate\NutsAndBolts\ServiceProvider;
use GeneralPurposeIO\Core\MagicAliases\Circuit;

class SeesawNeoSliderServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->container->singleton(SeesawNeoSliderMakeProfileCommand::class);
        $this->commands([
            SeesawNeoSliderMakeProfileCommand::class,
        ]);
    }

    public function boot(): void
    {
        Circuit::addCircuit(SeesawNeoSliderCatalogIc::SEESAW_NEO_SLIDER->value, SeesawNeoSlider::class);

        $maker = SeesawNeoSliderConsoleCommand::MAKE_PROFILE->value;
        foreach (SeesawNeoSliderCatalogIc::cases() as $ic) {
            Circuit::registerProfileCommand($ic->value, $maker);
        }
    }
}
