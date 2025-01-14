<?php
namespace FormalTheory\RegularExpression;

use FormalTheory\FiniteAutomata;

abstract class Token
{

    private $_to_string = NULL;

    abstract protected function _toString();

    abstract function getMatches();

    abstract protected function _compare($token);

    function __toString()
    {
        if (is_null($this->_to_string)) {
            $this->_to_string = $this->_toString();
        }
        return $this->_to_string;
    }

    function compare(self $token)
    {
        if (get_class($this) !== get_class($token)) {
            return FALSE;
        }
        return $this->_compare($token);
    }

    static function crossProductMatchArray($match_array1, $match_array2)
    {
        $output = array();
        foreach ($match_array1 as $match1) {
            foreach ($match_array2 as $match2) {
                $result = Expression::join($match1, $match2);
                if ($result === FALSE)
                    continue;
                $output[] = $result;
            }
        }
        return $output;
    }

    abstract function getFiniteAutomataClosure();

    function getNFA()
    {
        $create_loop_state = function (FiniteAutomata $fa) {
            $state = $fa->createState();
            foreach ($fa->getAlphabet() as $symbol) {
                $state->addTransition($symbol, $state);
            }
            return $state;
        };
        
        $fa = new FiniteAutomata(array_map("chr", range(0, 255)));
        
        $start_states = $fa->createStates(4);
        $end_states = $fa->createStates(4);
        
        $fa->setStartState($start_states[0]);
        $end_states[3]->setIsFinal(TRUE);
        
        $start_loop_state = $create_loop_state($fa);
        $start_states[0]->addTransition("", $start_loop_state);
        $start_loop_state->addTransition("", $start_states[2]);
        
        $end_loop_state = $create_loop_state($fa);
        $end_states[1]->addTransition("", $end_loop_state);
        $end_states[2]->addTransition("", $end_loop_state);
        $end_loop_state->addTransition("", $end_states[3]);
        
        $fa_closure = $this->getFiniteAutomataClosure();
        $fa_closure($fa, $start_states, $end_states);
        $fa->removeDeadStates();
        
        return $fa;
    }

    function getDFA()
    {
        $nfa = $this->getNFA();
        $dfa = $nfa->isDeterministic() ? $nfa : FiniteAutomata::determinize($nfa);
        unset($nfa);
        $dfa->minimize();
        return $dfa;
    }

    /**
     * @return int|null absolut number of the minimum length and null if unbounded
     */
    abstract public function getMinLength(): ?int;

    /**
     * @return int|null absolut number of the maximum length and null if unbounded
     */
    abstract public function getMaxLength(): ?int;
}
