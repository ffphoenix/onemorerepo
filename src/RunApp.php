<?php declare(strict_types=1);

use Test\App\App;

require __DIR__ . '/../vendor/autoload.php';

class RunApp
{
    public function run()
    {
        try {
            (new App())->exec();
        } catch (\Throwable $exception) {
            echo "Error: " . $exception->getMessage() . "\n";
        }
    }
}

(new RunApp())->run();