<?php

namespace AppBundle\Manager;

use Twilio\Twiml;

/**
* 
*/
class TwilioMessageManager
{
    private $twilio;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->twilio = new Twiml();
    }

    public function makeHomeMessage()
    {
        $response = $this->twilio;
        $response->say('Welcome to Csvey!', array("language" => "en-IN"));
        $response->say('Answer, Listen and Earn!', array("language" => "en-IN"));
        $response->gather(array("action" => "/homemessagehandler", "timeout"=>5,
            "method"=> "GET", "numDigits" => 1))->say("Press 1 for a survey question. 
                                    Press 2 for a health tips. 
                                    Press 3 for a news updates. 
                                    Press 4 for your current csvey balance .
                                    Press 5 to repeat the menu. ",
                                    array("language" => "en-IN"));
        $response->redirect('/outbound', array("method"=>"GET"));

        return $response;
    }
}