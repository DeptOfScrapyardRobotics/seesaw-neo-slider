<?php

use DeptOfScrapyardRobotics\Actuators\SeesawNeoSlider\NeopixelSlider;
use DeptOfScrapyardRobotics\Actuators\SeesawNeoSlider\SeesawNeoSlider;
use Fabricate\Contracts\Actuation\ActuatorException;
use Fabricate\Contracts\Circuits\IntegratedCircuit;
use Fabricate\NutsAndBolts\MagicAliases\Circuit;

it('delegates potentiometer and NeoPixel APIs to one seesaw NeoSlider', function (): void {
    $circuit = new class extends SeesawNeoSlider
    {
        /** @var list<int> */
        public array $pixelValues = [0, 0, 0, 0];

        public float $brightnessValue = 1.0;

        public int $shows = 0;

        public bool $closed = false;

        public function __construct() {}

        public function raw(): int
        {
            return 512;
        }

        public function position(): float
        {
            return 0.5;
        }

        public function pixelCount(): int
        {
            return count($this->pixelValues);
        }

        public function length(): int
        {
            return $this->pixelCount();
        }

        public function setPixelColor(
            int $pixel,
            int $color_or_red,
            ?int $green = null,
            ?int $blue = null,
            ?int $white = null,
        ): static {
            $this->pixelValues[$pixel] = $color_or_red;

            return $this;
        }

        public function getPixelColor(int $pixel): int
        {
            return $this->pixelValues[$pixel];
        }

        public function fill(
            int $color_or_red,
            ?int $green = null,
            ?int $blue = null,
            ?int $white = null,
        ): static {
            $this->pixelValues = array_fill(0, $this->pixelCount(), $color_or_red);

            return $this;
        }

        public function clear(): static
        {
            return $this->fill(0);
        }

        public function setBrightness(float $brightness): static
        {
            $this->brightnessValue = $brightness;

            return $this;
        }

        public function show(): static
        {
            $this->shows++;

            return $this;
        }

        public function close(): void
        {
            $this->closed = true;
        }
    };

    $slider = new NeopixelSlider($circuit);
    $slider
        ->fill(0x112233)
        ->setPixelColor(1, 0x445566)
        ->brightness(0.25)
        ->show();

    expect($slider->raw())->toBe(512)
        ->and($slider->position())->toBe(0.5)
        ->and($slider->pixelCount())->toBe(4)
        ->and($slider->length())->toBe(4)
        ->and($slider->getPixelColor(1))->toBe(0x445566)
        ->and($circuit->brightnessValue)->toBe(0.25)
        ->and($circuit->shows)->toBe(1);

    $slider->chase(0xABCDEF, delay_us: 0)->close();

    expect($circuit->pixelValues)->toBe([0, 0, 0, 0xABCDEF])
        ->and($circuit->shows)->toBe(5)
        ->and($circuit->closed)->toBeTrue();
});

it('requires a concrete seesaw NeoSlider circuit', function (): void {
    $constructor = new ReflectionMethod(NeopixelSlider::class, '__construct');
    $parameter = $constructor->getParameters()[0];

    expect($parameter->getType()?->getName())->toBe(SeesawNeoSlider::class);
});

it('rejects and closes a resolved circuit that is not a seesaw NeoSlider', function (): void {
    $wrongCircuit = new class implements IntegratedCircuit
    {
        public bool $closed = false;

        public function close(): void
        {
            $this->closed = true;
        }
    };

    Circuit::swap(new class($wrongCircuit)
    {
        public function __construct(
            protected IntegratedCircuit $circuit,
        ) {}

        public function driver(string $driver): IntegratedCircuit
        {
            return $this->circuit;
        }
    });

    try {
        expect(fn () => NeopixelSlider::circuit('wrong-circuit'))
            ->toThrow(
                ActuatorException::class,
                'Circuit [wrong-circuit] is not a SeesawNeoSlider.',
            );
    } finally {
        Circuit::clearResolvedInstance();
    }

    expect($wrongCircuit->closed)->toBeTrue();
});
