<?php

set_time_limit(60*60);

date_default_timezone_set("UTC");

use Symfony\Component\VarDumper\Dumper\CliDumper;
use Symfony\Component\VarDumper\Cloner\VarCloner;

class Dumper
{
    /**
     * Dump a value with elegance.
     *
     * @param  mixed  $value
     * @return void
     */
    public function dump($value)
    {
        if (class_exists(CliDumper::class)) {
            $dumper = 'cli' === PHP_SAPI ? new CliDumper : new HtmlDumper;

            $dumper->dump((new VarCloner)->cloneVar($value));
        } else {
            var_dump($value);
        }
    }
}

if (! function_exists('dd')) {
    /**
     * Dump the passed variables and end the script.
     *
     * @param  mixed
     * @return void
     */
    function dd()
    {
        array_map(function ($x) {
            (new Dumper)->dump($x);
        }, func_get_args());

        die(1);
    }
}

function _root_path($append = null) {
    return getcwd() . ($append ? DIRECTORY_SEPARATOR . $append : '' );
}

function _output_path($append = null) {
    return _root_path('output') . ($append ? DIRECTORY_SEPARATOR . $append : '' );
}