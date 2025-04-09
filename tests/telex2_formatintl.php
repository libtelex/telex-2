<?php declare(strict_types=1);

use Libtelex\Telex2\Telex2;

require '../vendor/autoload.php';

echo (new Telex2())->formatIntl('+44 (0)785 056 6978');  // => `"+44 7850 566978"`

echo "\n";
