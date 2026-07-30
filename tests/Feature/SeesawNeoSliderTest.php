<?php

use DeptOfScrapyardRobotics\Actuators\SeesawNeoSlider\SeesawNeoSlider;
use DeptOfScrapyardRobotics\Actuators\SeesawNeoSlider\SeesawNeoSliderException;
use GeneralPurposeIO\I2C\Drivers\I2CDriver;
use GeneralPurposeIO\I2C\I2CSlave;

final class FakeNeoSliderI2CDriver extends I2CDriver
{
    /** @var list<list<int>> */
    public array $writes = [];

    /** @var list<list<int>|false> */
    public array $responses = [];

    public bool $closed = false;

    public function probe(int $address): bool
    {
        return true;
    }

    public function read(int $address, int $len): array|false
    {
        return array_shift($this->responses) ?? false;
    }

    public function write(int $address, array|string $data): int
    {
        $bytes = is_string($data) ? array_values(unpack('C*', $data)) : array_values($data);
        $this->writes[] = $bytes;

        return count($bytes);
    }

    public function writeRead(int $address, array|string $bytes_to_write, int $bytes_to_read): array|false
    {
        $this->write($address, $bytes_to_write);

        return $this->read($address, $bytes_to_read);
    }

    public function bulkWrite(int $address, array|string $messages): array|false
    {
        return false;
    }

    public function close(): void
    {
        $this->closed = true;
    }
}

/** @return array{SeesawNeoSlider, FakeNeoSliderI2CDriver} */
function neoSliderFixture(int $product_id = 5295): array
{
    $driver = new FakeNeoSliderI2CDriver;
    $driver->responses = [
        [0x84],
        [($product_id >> 8) & 0xFF, $product_id & 0xFF, 0x00, 0x01],
    ];
    $slider = SeesawNeoSlider::fromI2CBus(new I2CSlave(0x30, $driver), boot_now: false);

    return [$slider, $driver];
}

it('validates PID 5295 and boots with all four pixels off', function (): void {
    [$slider, $driver] = neoSliderFixture();

    $slider->boot();

    expect($slider->hasBooted())->toBeTrue()
        ->and($slider->connected())->toBeTrue()
        ->and($slider->pixelCount())->toBe(4)
        ->and($driver->writes)->toBe([
            [0x00, 0x01],
            [0x00, 0x02],
            [0x0E, 0x01, 0x0E],
            [0x0E, 0x03, 0x00, 0x0C],
            [0x0E, 0x04, 0x00, 0x00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            [0x0E, 0x05],
        ]);
});

it('reads the 10-bit ADC and normalizes its position', function (): void {
    [$slider, $driver] = neoSliderFixture();
    $slider->boot();
    $driver->responses = [
        [0x02, 0x00],
        [0x03, 0xFF],
    ];

    expect($slider->raw())->toBe(512)
        ->and($slider->position())->toBe(1.0)
        ->and($driver->writes)->toContain([0x09, 0x19]);
});

it('accepts packed and separate RGB colors and writes one GRB pixel buffer', function (): void {
    [$slider, $driver] = neoSliderFixture();
    $slider->boot();
    $driver->writes = [];

    $slider
        ->setPixelColor(0, 0x112233)
        ->setPixelColor(1, 0x44, 0x55, 0x66)
        ->show();

    expect($driver->writes)->toBe([
        [0x0E, 0x04, 0x00, 0x00, 0x22, 0x11, 0x33, 0x55, 0x44, 0x66, 0, 0, 0, 0, 0, 0],
        [0x0E, 0x05],
    ]);
});

it('applies brightness without losing the source colors and can clear the strip', function (): void {
    [$slider, $driver] = neoSliderFixture();
    $slider->boot();
    $driver->writes = [];

    $slider->fill(100, 50, 20)->setBrightness(0.5)->show();
    $slider->setBrightness(1.0)->show();
    $slider->clear()->show();

    expect($driver->writes)->toBe([
        [0x0E, 0x04, 0x00, 0x00, 25, 50, 10, 25, 50, 10, 25, 50, 10, 25, 50, 10],
        [0x0E, 0x05],
        [0x0E, 0x04, 0x00, 0x00, 50, 100, 20, 50, 100, 20, 50, 100, 20, 50, 100, 20],
        [0x0E, 0x05],
        [0x0E, 0x04, 0x00, 0x00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
        [0x0E, 0x05],
    ]);
});

it('rejects a seesaw carrying the wrong product firmware', function (): void {
    [$slider] = neoSliderFixture(1234);

    expect(fn () => $slider->boot())
        ->toThrow(SeesawNeoSliderException::class, 'Expected seesaw product PID 5295, received 1234.');
});

it('closes its I2C bus', function (): void {
    [$slider, $driver] = neoSliderFixture();
    $slider->boot();

    $slider->close();

    expect($driver->closed)->toBeTrue()
        ->and($slider->connected())->toBeFalse();
});
