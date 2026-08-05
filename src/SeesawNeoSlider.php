<?php

namespace DeptOfScrapyardRobotics\Actuators\SeesawNeoSlider;

use DeptOfScrapyardRobotics\Actuators\SeesawNeoSlider\Enums\SliderSpecification;
use Fabricate\Contracts\Actuation\Interfaces\LEDStrip;
use Fabricate\Contracts\Actuation\Interfaces\Potentiometer;
use Fabricate\Contracts\Circuits\Attributes\IntegratedCircuit;
use Fabricate\Contracts\NutsAndBolts\BootSequence;
use GeneralPurposeIO\I2C\I2C;
use GeneralPurposeIO\I2C\I2CSlave;

#[IntegratedCircuit('I2C')]
class SeesawNeoSlider implements Potentiometer, LEDStrip, BootSequence
{
    /** @var list<array{red: int, green: int, blue: int}> */
    protected array $pixels = [];

    protected float $brightness = 1.0;

    protected bool $booted = false;

    public function __construct(
        protected readonly SeesawClient $seesaw,
        bool $boot_now = false,
    ) {
        $this->clear();

        if ($boot_now) {
            $this->boot();
        }
    }

    public function boot(): void
    {
        if ($this->booted) {
            return;
        }

        $product_id = $this->seesaw->status()['product_id'];

        if ($product_id !== SliderSpecification::PRODUCT_ID->value) {
            throw SeesawNeoSliderException::unexpectedProduct(
                SliderSpecification::PRODUCT_ID->value,
                $product_id,
            );
        }

        $this->seesaw->configureNeoPixels(
            SliderSpecification::NEOPIXEL_PIN->value,
            SliderSpecification::PIXEL_COUNT->value,
            SliderSpecification::BYTES_PER_PIXEL->value,
        );
        $this->clear();
        $this->transmitPixels();
        $this->booted = true;
    }

    public function hasBooted(): bool
    {
        return $this->booted;
    }

    public function connected(): bool
    {
        return $this->booted;
    }

    public function raw(): int
    {
        $this->ensureBooted();

        return $this->seesaw->analogRead(SliderSpecification::ANALOG_PIN->value);
    }

    public function position(): float
    {
        return max(0.0, min(1.0, $this->raw() / SliderSpecification::ADC_MAX->value));
    }

    public function setPixelColor(
        int $pixel,
        int $color_or_red,
        ?int $green = null,
        ?int $blue = null,
        ?int $white = null,
    ): static {
        if ($pixel < 0 || $pixel >= $this->pixelCount()) {
            throw SeesawNeoSliderException::invalidPixel($pixel, $this->pixelCount());
        }

        $this->pixels[$pixel] = $this->color($color_or_red, $green, $blue, $white);

        return $this;
    }

    public function getPixelColor(int $pixel): int
    {
        if ($pixel < 0 || $pixel >= $this->pixelCount()) {
            throw SeesawNeoSliderException::invalidPixel($pixel, $this->pixelCount());
        }

        $color = $this->pixels[$pixel];

        return ($color['red'] << 16) | ($color['green'] << 8) | $color['blue'];
    }

    public function fill(
        int $color_or_red,
        ?int $green = null,
        ?int $blue = null,
        ?int $white = null,
    ): static {
        $color = $this->color($color_or_red, $green, $blue, $white);
        $this->pixels = array_fill(0, $this->pixelCount(), $color);

        return $this;
    }

    public function setBrightness(float $brightness): static
    {
        if ($brightness < 0.0 || $brightness > 1.0) {
            throw SeesawNeoSliderException::invalidBrightness($brightness);
        }

        $this->brightness = $brightness;

        return $this;
    }

    public function clear(): static
    {
        return $this->fill(0, 0, 0);
    }

    public function show(): static
    {
        $this->ensureBooted();
        $this->transmitPixels();

        return $this;
    }

    public function pixelCount(): int
    {
        return SliderSpecification::PIXEL_COUNT->value;
    }

    public function length(): int
    {
        return $this->pixelCount();
    }

    public function close(): void
    {
        $this->seesaw->close();
        $this->booted = false;
    }

    public static function i2c(
        string|int $device,
        ?string $adapter = null,
        int $slave = SliderSpecification::DEFAULT_ADDRESS->value,
        bool $boot_now = true,
    ): static {
        $i2c = I2C::adapter($adapter)
            ->device($device)
            ->bus()
            ->slave($slave);

        return static::fromI2CBus($i2c, $boot_now);
    }

    public static function fromI2CBus(I2CSlave $i2c, bool $boot_now = true): static
    {
        return new static(new SeesawClient($i2c), $boot_now);
    }

    protected function ensureBooted(): void
    {
        if (! $this->booted) {
            $this->boot();
        }
    }

    /**
     * @return array{red: int, green: int, blue: int}
     */
    protected function color(
        int $color_or_red,
        ?int $green,
        ?int $blue,
        ?int $white,
    ): array {
        if (! is_null($white)) {
            throw SeesawNeoSliderException::unsupportedWhiteChannel();
        }

        if (is_null($green) && is_null($blue)) {
            if ($color_or_red < 0 || $color_or_red > 0xFFFFFF) {
                throw SeesawNeoSliderException::invalidColorChannel($color_or_red);
            }

            return [
                'red' => ($color_or_red >> 16) & 0xFF,
                'green' => ($color_or_red >> 8) & 0xFF,
                'blue' => $color_or_red & 0xFF,
            ];
        }

        if (is_null($green) || is_null($blue)) {
            throw SeesawNeoSliderException::invalidColorArguments();
        }

        foreach ([$color_or_red, $green, $blue] as $channel) {
            if ($channel < 0 || $channel > 255) {
                throw SeesawNeoSliderException::invalidColorChannel($channel);
            }
        }

        return [
            'red' => $color_or_red,
            'green' => $green,
            'blue' => $blue,
        ];
    }

    protected function transmitPixels(): void
    {
        $buffer = [];

        foreach ($this->pixels as $pixel) {
            $buffer[] = (int) ($pixel['green'] * $this->brightness);
            $buffer[] = (int) ($pixel['red'] * $this->brightness);
            $buffer[] = (int) ($pixel['blue'] * $this->brightness);
        }

        $this->seesaw->writeNeoPixelBuffer($buffer);
        $this->seesaw->showNeoPixels();
    }
}
