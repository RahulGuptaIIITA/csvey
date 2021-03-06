<?php

namespace AppBundle\Manager;

use Twilio\Twiml;

/**
* 
*/
class TwilioMessageHandlingManager
{   
    private $twilio;
    private $surveyManager;
    private $userManager;
    private $healthTipManager;
    private $newsManager;

    /**
     * Constructor
     */
    public function __construct($surveyManager,
        $userManager,
        $healthTipManager,
        $newsManager)
    {
        $this->twilio = new Twiml();
        $this->surveyManager = $surveyManager;
        $this->userManager = $userManager;
        $this->healthTipManager = $healthTipManager;
        $this->newsManager = $newsManager;
    }

    /**
    *This will handle all the home page
    */
    public function handleHomePage($queryParams)
    {
        $response = $this->twilio;

        switch ($queryParams['Digits']) {
            case "1":
                $survey = $this->surveyManager->getSurvey();
                $response->gather(array('action' => "/survey/".$survey['id'], "method" => "POST",
                "numDigits" => 1))->say($survey['text'], array("language" => "en-IN"));
                break;
            case "2":
                if (isset($queryParams['Called'])) {
                    $user = $this->userManager->loadByPhoneNumber($queryParams['Called']);
                    if ($user->getAge() == -1) {
                        $response->gather(array('action' => '/ageinformation', "method" => "POST",
                            "numDigits" => 2))->say("Please Provide your Age by pressing number keypad,
                        so that we can serve you the Best Health tips.", array("language" => "en-IN"));
                        $response->say("Sorry not a correct response", array("language" => "en-IN"));
                    } else {
                        $healthTip = $this->healthTipManager->getHealthTip($user->getAge());
                        $response->say($healthTip, array("language" => "en-IN"));
                    }
                }
                break;
            case "3":
                $newsText = $this->newsManager->getNews();
                $response->say($newsText, array("language" => "en-IN"));
                break;
            default:
                $response->say('sorry your response is not valid, switching to main menu', array("language" => "en-IN"));
        }
        $response->redirect('/outbound', array("method"=>"GET"));

        return $response;
    }
}