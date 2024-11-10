<?php

namespace DPRMC\ComplySciSpider;

use DPRMC\ComplySciSpider\BrowserHelpers\BrowserTrait;
use DPRMC\ComplySciSpider\BrowserHelpers\LoginTrait;
use DPRMC\ComplySciSpider\BrowserHelpers\ChromeDebugTrait;
use DPRMC\ComplySciSpider\BrowserHelpers\PoliticalRecipientsTrait;


class ComplySciSpider {

    use BrowserTrait;
    use ChromeDebugTrait;
    use LoginTrait;
    use PoliticalRecipientsTrait;


    /**
     * @param string      $companyName
     * @param string      $username
     * @param string      $password
     * @param string      $chromePath
     * @param string      $userAgentString
     * @param string      $pathToScreenshots
     * @param string|null $timezone
     *
     * @throws \HeadlessChromium\Exception\CommunicationException
     * @throws \HeadlessChromium\Exception\NoResponseAvailable
     * @throws \HeadlessChromium\Exception\OperationTimedOut
     */
    public function __construct( string $companyName,
                                 string $username,
                                 string $password,
                                 string $chromePath,
                                 string $userAgentString,
                                 string $pathToScreenshots = '.',
                                 string $timezone = NULL ) {

        $this->companyName       = $companyName;
        $this->username          = $username;
        $this->password          = $password;
        $this->chromePath        = $chromePath;
        $this->userAgentString   = $userAgentString;
        $this->pathToScreenshots = $pathToScreenshots;
        $this->timezone          = $timezone;

        $this->_createBrowser( $this->chromePath );
        $this->_createPage();
    }


}