<?php

use DeptOfScrapyardRobotics\Actuators\SeesawNeoSlider\Providers\SeesawNeoSliderServiceProvider;
use DeptOfScrapyardRobotics\Actuators\SeesawNeoSlider\SeesawClient;
use DeptOfScrapyardRobotics\Actuators\SeesawNeoSlider\SeesawNeoSlider;
use Fabricate\Contracts\Actuation\Interfaces\Potentiometer;
use Fabricate\Contracts\Circuits\Attributes\IntegratedCircuit;
use Fabricate\Contracts\NutsAndBolts\BootSequence;

it('advertises v0.6 provider discovery and registers the circuit slug', function (): void {
    $composer = json_decode(file_get_contents(dirname(__DIR__, 2).'/composer.json'), true, flags: JSON_THROW_ON_ERROR);
    $provider = file_get_contents(dirname(__DIR__, 2).'/src/Providers/SeesawNeoSliderServiceProvider.php');

    expect($composer['version'])->toBe('0.6.0')
        ->and($composer['autoload']['psr-4'])->toHaveKey(
            'DeptOfScrapyardRobotics\\Actuators\\SeesawNeoSlider\\',
        )
        ->and($composer['extra']['scrapyard-io']['providers'])->toContain(
            SeesawNeoSliderServiceProvider::class,
        )
        ->and($provider)->toContain("Circuit::addCircuit('seesaw-neo-slider'");
});

it('is an I2C-only bootable Fabricate potentiometer with a local seesaw client', function (): void {
    $reflection = new ReflectionClass(SeesawNeoSlider::class);
    $attributes = $reflection->getAttributes(IntegratedCircuit::class);

    expect(is_subclass_of(SeesawNeoSlider::class, Potentiometer::class))->toBeTrue()
        ->and(is_subclass_of(SeesawNeoSlider::class, BootSequence::class))->toBeTrue()
        ->and($attributes)->toHaveCount(1)
        ->and($attributes[0]->getArguments())->toBe(['I2C'])
        ->and(method_exists(SeesawNeoSlider::class, 'i2c'))->toBeTrue()
        ->and(method_exists(SeesawNeoSlider::class, 'fromI2CBus'))->toBeTrue()
        ->and(method_exists(SeesawNeoSlider::class, 'close'))->toBeTrue()
        ->and($reflection->getConstants())->toBe([])
        ->and((new ReflectionClass(SeesawClient::class))->getConstants())->toBe([])
        ->and(SeesawClient::class)->not->toContain('SeesawMiniGamepad');
});
