<?php

/**
 * <h2>Programming with Caleb</h2>
 * <p>Welcome to the Bot class.
 * This is where all the fun happens, in fact I have coded it in such a way that it works with
 * any form of data source that can implement arrays.<br>
 * This class makes use of the BotIT Interface to strictly implement the necessary use of a Bot.
 * </p>
 * @author Caleb Okpara <cletusokoys@gmail.com>
 */
class Bot {

    /**
     * This is the trigger for the bot to know that an event is taking place.
     * It can also be called a listener, it listens for messages and implements a callback function.
     * The callback function is there for the user of this library to implement its
     * functionalities without ChattyBot choking on them.
     *
     * @param String $message
     * @param callable $callback
     * @return String
     */
    public function hears($message, callable $callback) {
        $callback($message);
    }

    /**
     * This method returns a response to the user based on
     * the question asked and the array of answers the programmer provides.
     * Meanwhile the array of dictionary can be from any data source provided it
     * uses a simple key value pair array.
     * @param String $message
     * @param array $dictionary
     * @return String/null
     */
    public function generateResponseML($message, array $dictionary) {
        $message = trim(strtolower($message));
        $tok = new NlpTools\Tokenizers\WhitespaceAndPunctuationTokenizer();
        $J = new NlpTools\Similarity\CosineSimilarity();

        $setA = $tok->tokenize($message);
        $res = [];
        foreach ($dictionary as $dict => $value) {
            $setB = $tok->tokenize(trim(strtolower($dict)));
            $sim = $J->similarity($setA, $setB);
            array_push($res, $sim);
        }
        $max = max($res);

        foreach ($dictionary as $dict => $value) {
            $setB = $tok->tokenize(trim(strtolower($dict)));
            $sim = $J->similarity($setA, $setB);
            if ($max >= 0.35) {
                if ($sim == $max) {
                    return $value;
                }
            } else {
                return "";
            }
        }
//        $res = [];
    }

    /**
     * returns a reply
     * @param String $response
     * @return String
     */
    public function reply($response) {
        echo $response;
    }

    /**
     * Strips a string of the remaining values passed to it.
     * The first parameter is required
     * @param String $str
     * @param args $args
     * @return String
     */
    public function stripChars($str, ...$args) {
        foreach ($args as $ar) {
            $str = str_replace($ar, "", $str);
        }
        return $str;
    }

}
