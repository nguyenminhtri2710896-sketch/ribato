<?php 

namespace App\Console\Commands;

trait PrependsOutput
{
    public function line($string, $style = null, $verbosity = null)
    {
        parent::line($this->prepend($string), $style, $verbosity);
    }

    protected function prepend($string)
    {
        if (method_exists($this, 'getPrependString')) {
            return $this->getPrependString($string) . $string;
        }

        return $string;
    }
}