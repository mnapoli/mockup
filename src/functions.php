<?php

namespace Mockup;

/**
 * @return Mock
 */
function mock($class, array $methods = []) {
    return new Mock($class, $methods);
}
