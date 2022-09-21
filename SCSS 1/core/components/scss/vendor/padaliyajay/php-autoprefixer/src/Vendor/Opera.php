<?php
namespace Padaliyajay\PHPAutoprefixer\Vendor;

use Padaliyajay\PHPAutoprefixer\Vendor\Vendor;

class Opera extends Vendor {
    protected static $RULE_PROPERTY = array(
        'linear-gradient' => '-o-linear-gradient',
        'transform' => '-o-transform',
        'transform-origin' => '-o-transform-origin',
        'transition' => '-o-transition',
        'transition-delay' => '-o-transition-delay',
        'transition-duration' => '-o-transition-duration',
        'transition-property' => '-o-transition-property',
        'transition-timing-function' => '-o-transition-timing-function',
		'filter' => '-o-filter',
    );
}

