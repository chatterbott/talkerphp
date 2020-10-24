<?php

class Chatty extends Bot {

    private $isDefined = false;
    private $dataSource;
    private $response = null;

    /**
     * This method accepts an array as its parameter.
     * If the data is from a database it must first off be converted to a
     * simple key, value pair array.
     * @param array $data
     * @returns Boolean
     */
    public function defineDataSource($data) {
        if ($data != null) {
            $this->dataSource = $data;
            $this->isDefined = true;
        }
    }

    /**
     *
     * @param String $message
     * @param array $charsToStrip
     * @throws Exception
     */
    public function chat($message, array $charsToStrip = null) {
        if ($this->isDefined) {

            if ($charsToStrip != null) {
                $message = $this->stripChars($message, $charsToStrip);
            }

            $this->hears($message, function() {
                $msg = func_get_arg(0);
                $generatedResponse = $this->generateResponseML($msg, $this->dataSource);
                $this->response = $generatedResponse;
            });
        } else {
            $ex = new Exception("Datasource is not defined.");
            exit("<div class='error'>TalkerPHP Throws an IllegalStateException: " . $ex->getMessage() . "</div>");
        }
    }

    public function getResponse() {
        return $this->response;
    }

}
