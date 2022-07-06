<?php


namespace CakephpTestSuiteLight\Test;


class TestUtil
{
    public static function makeUuid(): string
    {
        return '123e4567-e89b-12d3-a456-' . rand(100000000000, 999999999999);
    }
}