<?php
namespace FormalTheory\RegularExpression\Token;

use FormalTheory\RegularExpression\Expression;
use FormalTheory\RegularExpression\Token;

class Special extends Token
{

    const BOS = '^';

    const EOS = '$';

    private $_special;

    static function getList()
    {
        return array(
            self::BOS,
            self::EOS
        );
    }

    function __construct($special)
    {
        if (! in_array($special, self::getList())) {
            throw new \RuntimeException("bad special");
        }
        $this->_special = $special;
    }

    function _toString()
    {
        return $this->_special;
    }

    function getMatches()
    {
        $lookup = array(
            self::BOS => "createFromBOS",
            self::EOS => "createFromEOS"
        );
        $function_name = $lookup[$this->_special];
        return array(
            Expression::$function_name()
        );
    }

    function isBOS()
    {
        return $this->_special === self::BOS;
    }

    function isEOS()
    {
        return $this->_special === self::EOS;
    }

    function getFiniteAutomataClosure()
    {
        $special = $this->_special;
        return function ($fa, $start_states, $end_states) use($special) {
            switch ($special) {
                case Special::BOS:
                    $start_states[0]->addTransition("", $end_states[1]);
                    $start_states[1]->addTransition("", $end_states[1]);
                    break;
                case Special::EOS:
                    $start_states[1]->addTransition("", $end_states[3]);
                    $start_states[2]->addTransition("", $end_states[3]);
                    $start_states[3]->addTransition("", $end_states[3]);
                    break;
                default:
                    throw new \Exception("should be unreachable");
            }
        };
    }

    protected function _compare($token)
    {
        return $this->_special === $token->_special;
    }

    public function getMinLength(): ?int
    {
        return 0;
    }

    public function getMaxLength(): ?int
    {
        return 0;
    }


}
