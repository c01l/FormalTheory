<?php
namespace FormalTheory\Tests\RegularExpression;

use FormalTheory\RegularExpression\Lexer;
use FormalTheory\FiniteAutomata;

class DoesIntersectTest extends \PhpUnit\Framework\TestCase
{

    function dataProviderForTestDoesIntersect()
    {
        return array(
            array(
                '^1{6,10}$',
                '^1{2,6}$',
                TRUE
            ),
            array(
                '^1{6,10}$',
                '^1{2,5}$',
                FALSE
            ),
            array(
                '^1{6,10}$',
                '^0{2,6}$',
                FALSE
            ),
            array(
                '^1*$',
                '^1{100}$',
                TRUE
            ),
            array(
                '^1(11)*$',
                '^(11)*$',
                FALSE
            ),
            array(
                '^(11)*1$',
                '^(11)*$',
                FALSE
            ),
            array(
                '^1(11)*$',
                '^(11111)*$',
                TRUE
            ),
            array(
                '^(1|0)*111(1|0)*$',
                '^(1|0)*000(1|0)*$',
                TRUE
            ),
            array(
                '1',
                '0',
                TRUE
            ),
            array(
                '^[a-z]*$',
                '^[A-Z]*$',
                TRUE
            ),
            array(
                '^.*1$',
                '^.*0$',
                FALSE
            ),
            array(
                '1$',
                '0$',
                FALSE
            ),
            array(
                '^1',
                '0$',
                TRUE
            ),
            array(
                '^1',
                '^0',
                FALSE
            ),
            array(
                '^(1|2){5}3$',
                '^1.*2.*3$',
                TRUE
            ),
            array(
                '^[1-9][0-9]*(\.[0-9]+)?$',
                '^3.14159265$',
                TRUE
            ),
            array(
                '^[1-9][0-9]*(\.[0-9]+)?$',
                '^42$',
                TRUE
            ),
            array(
                '1^0',
                '^.*$',
                FALSE
            ),
            array(
                '1$0',
                '^.*$',
                FALSE
            ),
            array(
                '1^0',
                '',
                FALSE
            ),
            array(
                '1$0',
                '',
                FALSE
            ),
            array(
                '^$',
                '',
                TRUE
            ),
            array(
                '^$',
                '^',
                TRUE
            ),
            array(
                '^$',
                '$',
                TRUE
            ),
            array(
                '^$',
                '^^$',
                TRUE
            ),
            array(
                '^$',
                '^$$',
                TRUE
            ),
            array(
                '^((bbbbb)*|(bbbbbbb)*)$',
                '^((b)*|(bb)*)$',
                TRUE
            )
        );
    }

    /**
     * @dataProvider dataProviderForTestDoesIntersect
     */
    function testDoesIntersect($regex_string_1, $regex_string_2, $expected_does_intersect)
    {
        $lexer = new Lexer();
        $nfa1 = $lexer->lex($regex_string_1)->getNFA();
        $nfa2 = $lexer->lex($regex_string_2)->getNFA();
        $this->assertSame($expected_does_intersect, FiniteAutomata::intersection($nfa1, $nfa2)->validSolutionExists());
        $this->assertSame($expected_does_intersect, FiniteAutomata::intersection($nfa2, $nfa1)->validSolutionExists());
    }
}
