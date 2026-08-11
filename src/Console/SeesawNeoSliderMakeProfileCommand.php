<?php

namespace DeptOfScrapyardRobotics\Actuators\SeesawNeoSlider\Console;

use DeptOfScrapyardRobotics\Actuators\SeesawNeoSlider\Enums\SeesawNeoSliderCatalogIc;
use Fabricate\Console\Command;
use GeneralPurposeIO\Circuits\CircuitRegistry;
use GeneralPurposeIO\Circuits\Console\Concerns\ScaffoldsCircuitProfiles;
use GeneralPurposeIO\Circuits\Support\CircuitAttributeInspector;
use GeneralPurposeIO\Contracts\Circuits\CircuitException;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'seesaw-neo-slider:make-profile')]
class SeesawNeoSliderMakeProfileCommand extends Command
{
    use ScaffoldsCircuitProfiles;

    protected ?string $signature = 'seesaw-neo-slider:make-profile
                    {ic? : Catalog slug (seesaw-neo-slider)}
                    {name? : Profile key to write into config/circuits.php}
                    {--protocol= : Protocol option label or factory name when non-interactive}';

    protected string $description = 'Scaffold a circuits.php profile for a Seesaw NeoSlider';

    public function handle(CircuitRegistry $registry): int
    {
        $available = array_values(array_filter(
            SeesawNeoSliderCatalogIc::slugs(),
            static fn (string $ic): bool => isset($registry->listCircuits()[$ic]),
        ));

        if ($available === []) {
            $this->components->error('No Seesaw NeoSlider ICs are registered.');

            return self::FAILURE;
        }

        $ic = $this->argument('ic');
        if (is_null($ic) || $ic === '') {
            $ic = $this->choice('Which Seesaw NeoSlider IC?', $available);
        }

        $ic = (string) $ic;

        if (is_null(SeesawNeoSliderCatalogIc::tryFrom($ic))) {
            $this->components->error("IC [{$ic}] is not a Seesaw NeoSlider.");

            return self::FAILURE;
        }

        try {
            $options = CircuitAttributeInspector::protocolOptions($registry->resolveClass($ic));
        } catch (CircuitException $e) {
            $this->components->error($e->getMessage());

            return self::FAILURE;
        }

        $selected = $this->resolveProtocolOption($options);
        if (is_null($selected)) {
            return self::FAILURE;
        }

        $name = $this->argument('name');
        if (is_null($name) || $name === '') {
            $name = $this->ask('Profile name', $ic);
        }

        return $this->writePromptedProfile($ic, (string) $name, $selected);
    }
}
