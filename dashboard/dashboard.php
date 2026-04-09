<?php
@session_start();
class DASHBOARD
{
    private $documentRoot;
    function __construct() {
        $codeSource = 'bsis';
        $documentRoot = $_SERVER['DOCUMENT_ROOT'].'/'.$codeSource.'/';
        // signInCheck();
        $this->documentRoot = $documentRoot;
    }
    
}
?>