<?php
namespace FormalTheory\Tests\FiniteAutomata;

use FormalTheory\FiniteAutomata;
use PHPUnit\Framework\TestCase;

class BasicTest extends TestCase
{

    function dataProviderForTestBasic()
    {
        $even = function () {
            $fa = new FiniteAutomata(array(
                "0"
            ));
            list ($a, $b) = $fa->createStates(2);
            $fa->setStartState($a);
            $a->setIsFinal(TRUE);
            $a->addTransition("0", $b);
            $b->addTransition("0", $a);
            return $fa;
        };
        $odd = function () {
            $fa = new FiniteAutomata(array(
                "0"
            ));
            list ($a, $b) = $fa->createStates(2);
            $fa->setStartState($a);
            $b->setIsFinal(TRUE);
            $a->addTransition("0", $b);
            $b->addTransition("0", $a);
            return $fa;
        };
        $threes = function () {
            $fa = new FiniteAutomata(array(
                "0"
            ));
            list ($a, $b, $c) = $fa->createStates(3);
            $fa->setStartState($a);
            $a->setIsFinal(TRUE);
            $a->addTransition("0", $b);
            $b->addTransition("0", $c);
            $c->addTransition("0", $a);
            return $fa;
        };
        return array(
            array(
                function () {
                    $fa = new FiniteAutomata(array(
                        "0",
                        "1"
                    ));
                    list ($a, $b, $c) = $fa->createStates(3);
                    $fa->setStartState($a);
                    $c->setIsFinal(TRUE);
                    $a->addTransition("0", $a);
                    $b->addTransition("0", $b);
                    $c->addTransition("0", $c);
                    $a->addTransition("1", $b);
                    $b->addTransition("1", $c);
                    return $fa;
                },
                "^0*10*10*$"
            ),
            array(
                $even,
                "^(00)*$"
            ),
            array(
                $odd,
                "^0(00)*$"
            ),
            array(
                $threes,
                "^(000)*$"
            ),
            array(
                function () use($even, $odd) {
                    return FiniteAutomata::union($even(), $odd());
                },
                "^0*$"
            ),
            array(
                function () use($even, $odd) {
                    return FiniteAutomata::intersection($even(), $odd());
                },
                FALSE
            ),
            array(
                function () use($even, $threes) {
                    return FiniteAutomata::intersection($even(), $threes());
                },
                "^(000000)*$"
            ),
            array(
                function () use($even, $odd) {
                    return FiniteAutomata::intersection($even(), FiniteAutomata::union($even(), $odd()));
                },
                "^(00)*$"
            )
        );
    }

    /**
     * @dataProvider dataProviderForTestBasic
     */
    function testBasic($fa_closure, $compare)
    {
        $fa = $fa_closure();
        
        $test_numbers = array();
        $max = pow(2, 6) * 2;
        for ($i = 2; $i < $max; $i ++) {
            $number = substr(base_convert($i, 10, 2), 1);
            $test_numbers[] = $number;
        }
        $test_numbers = array_unique($test_numbers);
        if ($compare) {
            $this->assertTrue($fa->validSolutionExists());
            foreach ($test_numbers as $test_number) {
                $this->assertSame((bool) preg_match("/$compare/", $test_number), $fa->isMatch(str_split($test_number)), $test_number);
            }
        } else {
            $this->assertFalse($fa->validSolutionExists());
        }
    }
}
