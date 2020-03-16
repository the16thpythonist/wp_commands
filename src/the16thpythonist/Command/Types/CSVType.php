<?php


namespace the16thpythonist\Command\Types;


class CSVType extends ParameterType
{
    public static function apply(string $parameter)
    {
        return str_getcsv($parameter);
    }

    public static function check($value): bool
    {
        return is_array($value);
    }
}