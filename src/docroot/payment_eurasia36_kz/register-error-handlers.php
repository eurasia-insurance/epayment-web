<?php

ob_start();

set_exception_handler('myExceptionHandler');
set_error_handler('myErrorHandler');

function to500() {
    ob_end_clean();
    http_response_code(500);
#    header("Location: /500.html");

    include $_SERVER[DOCUMENT_ROOT].'/500.html';
    die;
}

function myExceptionHandler($ex) {
    error_log("Uncaught exception class=" . get_class($ex) . " message=" . $ex->getMessage() . " line=" . $ex->getLine());
    to500();
    die;
}

function myErrorHandler($errno, $errstr, $errfile, $errline) {
    error_log("Fatal error errno=" . $errno . " errstr=" . $errstr . " errfile=" . $errfile . " errline=" . $errline);
    to500();
    die;
}
