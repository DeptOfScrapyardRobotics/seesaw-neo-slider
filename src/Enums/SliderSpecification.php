<?php

namespace DeptOfScrapyardRobotics\Actuators\SeesawNeoSlider\Enums;

enum SliderSpecification: int
{
    case DEFAULT_ADDRESS = 0x30;
    case PRODUCT_ID = 5295;
    case ANALOG_PIN = 18;
    case NEOPIXEL_PIN = 14;
    case PIXEL_COUNT = 4;
    case BYTES_PER_PIXEL = 3;
    case ADC_MAX = 1023;
}
