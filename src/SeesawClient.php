<?php

namespace DeptOfScrapyardRobotics\Actuators\SeesawNeoSlider;

use DeptOfScrapyardRobotics\Actuators\SeesawNeoSlider\Enums\SeesawCommand;
use GeneralPurposeIO\I2C\I2CSlave;

class SeesawClient
{
    public function __construct(protected readonly I2CSlave $i2c) {}

    /** @return array{hardware_id: int, version: int, product_id: int} */
    public function status(): array
    {
        $hardware_id = $this->hardwareId();
        $version = $this->version();

        return [
            'hardware_id' => $hardware_id,
            'version' => $version,
            'product_id' => ($version >> 16) & 0xFFFF,
        ];
    }

    public function hardwareId(): int
    {
        return $this->readRegister(SeesawCommand::STATUS_HARDWARE_ID, 1)[0];
    }

    public function version(): int
    {
        return $this->uint32($this->readRegister(SeesawCommand::STATUS_VERSION, 4));
    }

    public function analogRead(int $pin): int
    {
        $bytes = $this->readRegister(SeesawCommand::ADC_CHANNEL, 2, $pin, 500);

        return (($bytes[0] << 8) | $bytes[1]) & 0xFFFF;
    }

    public function configureNeoPixels(int $pin, int $pixel_count, int $bytes_per_pixel = 3): void
    {
        $this->writeRegister(SeesawCommand::NEOPIXEL_PIN, [$pin]);
        $this->writeRegister(
            SeesawCommand::NEOPIXEL_BUFFER_LENGTH,
            $this->uint16Bytes($pixel_count * $bytes_per_pixel),
        );
    }

    /** @param list<int> $buffer */
    public function writeNeoPixelBuffer(array $buffer): void
    {
        for ($offset = 0; $offset < count($buffer); $offset += 30) {
            $chunk = array_slice($buffer, $offset, 30);
            $this->writeRegister(
                SeesawCommand::NEOPIXEL_BUFFER,
                [...$this->uint16Bytes($offset), ...$chunk],
            );
        }
    }

    public function showNeoPixels(): void
    {
        $this->writeRegister(SeesawCommand::NEOPIXEL_SHOW, []);
    }

    public function close(): void
    {
        $this->i2c->close();
    }

    /**
     * @return list<int>
     */
    protected function readRegister(
        SeesawCommand $command,
        int $length,
        int $channel = 0,
        int $delay_us = 250,
    ): array {
        $register = [$command->module(), $command->register($channel)];
        $written = $this->i2c->write($register);

        if ($written !== count($register)) {
            throw SeesawNeoSliderException::i2cWriteFailed(count($register), $written);
        }

        if ($delay_us > 0) {
            usleep($delay_us);
        }

        $bytes = $this->i2c->read($length);

        if ($bytes === false || count($bytes) !== $length) {
            throw SeesawNeoSliderException::i2cReadFailed($length);
        }

        return array_values($bytes);
    }

    /** @param list<int> $payload */
    protected function writeRegister(SeesawCommand $command, array $payload): void
    {
        $bytes = [$command->module(), $command->register(), ...$payload];
        $written = $this->i2c->write($bytes);

        if ($written !== count($bytes)) {
            throw SeesawNeoSliderException::i2cWriteFailed(count($bytes), $written);
        }
    }

    /** @return list<int> */
    protected function uint16Bytes(int $value): array
    {
        return [
            ($value >> 8) & 0xFF,
            $value & 0xFF,
        ];
    }

    /** @param list<int> $bytes */
    protected function uint32(array $bytes): int
    {
        return (($bytes[0] << 24) | ($bytes[1] << 16) | ($bytes[2] << 8) | $bytes[3]) & 0xFFFFFFFF;
    }
}
