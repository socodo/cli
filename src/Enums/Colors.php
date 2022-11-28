<?php

namespace Socodo\CLI\Enums;

enum Colors: string
{
    case RESET = "\33[0m";

    case BLACK = "\33[0;30m";
    case DARK_GRAY = "\33[1;30m";

    case RED = "\33[0;31m";
    case LIGHT_RED = "\33[1;31m";

    case GREEN = "\33[0;32m";
    case LIGHT_GREEN = "\33[1;32m";

    case BROWN = "\33[0;33m";
    case LIGHT_BROWN = "\33[1;33m";

    case BLUE = "\33[0;34m";
    case LIGHT_BLUE = "\33[1;34m";

    case PURPLE = "\33[0;35m";
    case LIGHT_PURPLE = "\33[1;35m";

    case CYAN = "\33[0;36m";
    case LIGHT_CYAN = "\33[1;36m";

    case LIGHT_GRAY = "\33[0;37m";
    case WHITE = "\33[1;37m";
}