<?php
//Refactoring, if needed
//Before going on with task v3, let's discuss the code and/or implement some additional functionality.
//
//Task v3
//Before or while performing this task, consider refactoring your code into OOP.
//
//Maintain the code from task v1 and v2, as it's still used in the system.
//
//For numbers from 1 to 10:
//
//Print that number
//If number is one of the numbers 1, 4, 9, print joff instead
//If number is larger than 5, print tchoff instead
//If number is one of the numbers 1, 4, 9 and is larger than 5, print jofftchoff instead
//Put dashes in between of the elements (but not before first or after the last one).
//
//Expected output:
//
//joff-2-3-joff-5-tchoff-tchoff-tchoff-jofftchoff-tchoff
//As task v1 and v2 should be mainained, full expected output would be something like this:
//
//Task v1:
//1 2 pa 4 pow pa 7 8 pa pow 11 pa 13 14 papow 16 17 pa 19 pow
//Task v2:
//1-hatee-3-hatee-5-hatee-ho-hatee-9-hatee-11-hatee-13-hateeho-15
//Task v3:
//joff-2-3-joff-5-tchoff-tchoff-tchoff-jofftchoff-tchoff
//It would be really great if there would be no copy-and-paste (you can use copy-and-paste, just avoid duplicated code in the end result).
//
//Parameters and logic can be changed or added - it's best if your code would be easily maintanable, extensible and optionally testable.
//
//For example, more conditions can be added for new tasks (less than 3, between 5 and 11, is primary etc.), this should be easily added to the code.
interface Formatter
{
    public function format(int $input): string;
}

class AForrmatter implements Formatter
{
    public function format(int $input): string
    {
        if ($input % 5 === 0 && $input % 3 === 0) {
            return 'papow';
        }
        if ($input % 3 === 0) {
            return 'pa';
        }
        if ($input % 5 === 0) {
            return 'pow';
        }
        return (string)$input;
    }
}
class BForrmatter implements Formatter
{
    public function format(int $input): string
    {
        if ($input % 2 === 0 && $input % 7 === 0) {
            return 'hateeho';
        }
        if ($input % 2 === 0) {
            return 'hatee';
        }
        if ($input % 7 === 0) {
            return 'ho';
        }
        return (string)$input;
    }
}

class CForrmatter implements Formatter
{
    public function format(int $input): string
    {
        if ($input === 9) {
            return 'jofftchoff';
        }

        if (in_array($input, [1, 4])) {
            return 'joff';
        }

        if ($input > 5) {
            return 'tchoff';
        }
        return (string)$input;
    }
}

class Sequence
{
    private Formatter $formatter;
    public function __construct(Formatter $formatter)
    {
        $this->formatter = $formatter;
    }

    function print(int $start, int $end, string $separator = ' ',): string
    {
        $string = '';
        for ($i = $start; $i <= $end; $i++) {
            $string .= $this->formatter->format($i);
            if ($i != $end) {
                $string .= $separator;
            }
        }
        return $string;
    }
}


echo (new Sequence((new AForrmatter())))->print(1, 20, ' ') . "\n";
echo (new Sequence((new BForrmatter())))->print(1, 15, '-') . "\n";
echo (new Sequence((new CForrmatter())))->print(1, 10, '-') . "\n";
