<?php

/**
 * Octopus Util
 *
 * @author milan
 *
 */
class Octopus_Util {

    /**
     * Return command line args for --param1=value as assoc array
     * 
     * @return array
     */
    public static function arguments() {
        $args = $_SERVER['argv'];
        array_shift($args);
        $args = join($args, ' ');

        preg_match_all('/ (--\w+ (?:[= ] [^-]+ [^\s-] )? ) | (-\w+) | (\w+) /x', $args, $match);
        $args = array_shift($match);

        /*
          Array
          (
          [0] => asdf
          [1] => asdf
          [2] => --help
          [3] => --dest=/var/
          [4] => -asd
          [5] => -h
          [6] => --option mew arf moo
          [7] => -z
          )
         */

        $ret = array(
            'input' => array(),
            'commands' => array(),
            'flags' => array()
        );

        foreach ($args as $arg) {

            // Is it a command? (prefixed with --)
            if (substr($arg, 0, 2) === '--') {

                $value = preg_split('/[= ]/', $arg, 2);
                $com = substr(array_shift($value), 2);
                $value = join($value);

                $ret['commands'][$com] = !empty($value) ? $value : true;
                continue;
            }

            // Is it a flag? (prefixed with -)
            if (substr($arg, 0, 1) === '-') {
                $ret['flags'][] = substr($arg, 1);
                continue;
            }

            $ret['input'][] = $arg;
            continue;
        }

        return $ret;
    }

}

/* vi: set ts=8 sw=4 sts=4 noet: */