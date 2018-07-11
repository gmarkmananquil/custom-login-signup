<?php
/**
 *
 *
 **/

class AlertMessage{
    const ERROR = "error";
    const SUCCESS = "success";
    const INFO = "info";

    public $type;
    public $message;
	public $element_id;
    
    public function __construct($type, $message, $elementId = ""){
        $this->type = $type;
        $this->message = $message;
        $this->element_id = $elementId;
    }
}
