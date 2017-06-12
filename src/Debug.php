<?php

namespace Queue;

class Debug
{
    public function dump(...$args)
    {
        var_dump($args);
    }
}
