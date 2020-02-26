<?php

use PHPUnit\Framework\TestCase;
use App\Lib\Pluralizer;

class PluralizerTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_pluralize_any_word()
    {
        $words = [
            [
                'singular' => 'car',
                'plural'   => 'cars',
            ],
            [
                'singular' => 'bus',
                'plural'   => 'buses',
            ],
            [
                'singular' => 'penny',
                'plural'   => 'pennies',
            ],
            [
                'singular' => 'user',
                'plural'   => 'users',
            ],
            [
                'singular' => 'baby',
                'plural'   => 'babies',
            ],
            [
                'singular' => 'city',
                'plural'   => 'cities',
            ],
            [
                'singular' => 'box',
                'plural'   => 'boxes',
            ],
        ];

        foreach ($words as $word)
        {
            $this->assertEquals($word['plural'], Pluralizer::pluralize($word['singular']));
        }
    }
}