<?php

namespace DeptOfScrapyardRobotics\Actuators\SeesawNeoSlider\Enums;

enum SeesawNeoSliderCatalogIc: string
{
    case SEESAW_NEO_SLIDER = 'seesaw-neo-slider';

    /**
     * @return list<string>
     */
    public static function slugs(): array
    {
        return array_map(
            static fn (self $case): string => $case->value,
            self::cases(),
        );
    }
}
