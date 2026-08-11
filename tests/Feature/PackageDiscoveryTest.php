<?php

use DeptOfScrapyardRobotics\Actuators\SeesawNeoSlider\Enums\SeesawNeoSliderCatalogIc;
use DeptOfScrapyardRobotics\Actuators\SeesawNeoSlider\Enums\SeesawNeoSliderConsoleCommand;
use DeptOfScrapyardRobotics\Actuators\SeesawNeoSlider\SeesawClient;
use DeptOfScrapyardRobotics\Actuators\SeesawNeoSlider\SeesawNeoSlider;
use DeptOfScrapyardRobotics\Actuators\SeesawNeoSlider\SeesawNeoSliderException;
use DeptOfScrapyardRobotics\Actuators\SeesawNeoSlider\SeesawNeoSliderServiceProvider;
use GeneralPurposeIO\Contracts\Circuits\Attributes\IntegratedCircuit;
use GeneralPurposeIO\Contracts\Circuits\BootSequence;
use Waveforms\Contracts\Actuation\ActuatorException;
use Waveforms\Contracts\Actuation\Interfaces\LEDStrip;
use Waveforms\Contracts\Actuation\Interfaces\Potentiometer;

it('advertises v0.7 provider discovery and registers the circuit catalog slug', function (): void {
    $composer = json_decode(file_get_contents(dirname(__DIR__, 2).'/composer.json'), true, flags: JSON_THROW_ON_ERROR);
    $provider = file_get_contents(dirname(__DIR__, 2).'/src/SeesawNeoSliderServiceProvider.php');

    expect($composer['version'])->toBe('0.7.0')
        ->and($composer['require'])->toHaveKey('scrapyard-io/gpio-framework')
        ->and($composer['require'])->toHaveKey('scrapyard-io/waveforms')
        ->and($composer['autoload']['psr-4'])->toHaveKey(
            'DeptOfScrapyardRobotics\\Actuators\\SeesawNeoSlider\\',
        )
        ->and($composer['extra']['scrapyard-io']['providers'])->toContain(
            SeesawNeoSliderServiceProvider::class,
        )
        ->and($provider)->toContain('Circuit::addCircuit')
        ->and($provider)->toContain('SeesawNeoSliderCatalogIc::SEESAW_NEO_SLIDER')
        ->and($provider)->not->toContain('addActuator')
        ->and(SeesawNeoSliderCatalogIc::SEESAW_NEO_SLIDER->value)->toBe('seesaw-neo-slider')
        ->and(SeesawNeoSliderConsoleCommand::MAKE_PROFILE->value)->toBe('seesaw-neo-slider:make-profile');
});

it('is an I2C-only bootable potentiometer and LED strip with a local seesaw client', function (): void {
    $reflection = new ReflectionClass(SeesawNeoSlider::class);
    $attributes = $reflection->getAttributes(IntegratedCircuit::class);

    expect(is_subclass_of(SeesawNeoSlider::class, Potentiometer::class))->toBeTrue()
        ->and(is_subclass_of(SeesawNeoSlider::class, LEDStrip::class))->toBeTrue()
        ->and(is_subclass_of(SeesawNeoSlider::class, BootSequence::class))->toBeTrue()
        ->and(is_subclass_of(SeesawNeoSliderException::class, ActuatorException::class))->toBeTrue()
        ->and($attributes)->toHaveCount(1)
        ->and($attributes[0]->getArguments())->toBe(['I2C'])
        ->and(method_exists(SeesawNeoSlider::class, 'i2c'))->toBeTrue()
        ->and(method_exists(SeesawNeoSlider::class, 'fromI2CBus'))->toBeTrue()
        ->and(method_exists(SeesawNeoSlider::class, 'close'))->toBeTrue()
        ->and($reflection->getConstants())->toBe([])
        ->and((new ReflectionClass(SeesawClient::class))->getConstants())->toBe([])
        ->and(SeesawClient::class)->not->toContain('SeesawMiniGamepad');
});
