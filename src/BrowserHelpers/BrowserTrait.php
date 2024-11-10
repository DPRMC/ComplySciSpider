<?php

namespace DPRMC\ComplySciSpider\BrowserHelpers;


use HeadlessChromium\Browser\ProcessAwareBrowser;
use HeadlessChromium\BrowserFactory;
use HeadlessChromium\Cookies\CookiesCollection;
use HeadlessChromium\Page;

/**
 *
 */
trait BrowserTrait {

    /**
     * @var string Used as a prefix on all URLs.
     */
    protected string $companyName;
    protected string $username;
    protected string $password;

    protected $loginUrlSuffix = '.complysci.com/Membership/Login';


    protected CookiesCollection $cookies;
    protected string              $chromePath;
    protected ProcessAwareBrowser $Browser;
    protected Page                $Page;


    protected string $defaultUserAgentString  = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/104.0.5112.79 Safari/537.36';
    protected string $userAgentString;
    protected int    $browserConnectionDelay  = 30;
    protected int    $browserWindowSizeWidth  = 1600;
    protected int    $browserWindowSizeHeight = 1000;
    protected bool   $browserEnableImages     = TRUE;
    protected string $timezone                = 'America/Denver';


    /**
     * @throws \HeadlessChromium\Exception\CommunicationException
     * @throws \HeadlessChromium\Exception\OperationTimedOut
     * @throws \HeadlessChromium\Exception\NoResponseAvailable
     */
    protected function _createBrowser( string $chromePath ): void {
        $this->cookies    = new CookiesCollection();
        $this->chromePath = $chromePath;

        $browserFactory = new BrowserFactory( $this->chromePath );
        // starts headless chrome
        $this->Browser = $browserFactory->createBrowser( [
                                                             'headless'        => TRUE,         // disable headless mode
                                                             'connectionDelay' => $this->browserConnectionDelay,
                                                             //'debugLogger'     => 'php://stdout', // will enable verbose mode
                                                             'windowSize'      => [ $this->browserWindowSizeWidth,
                                                                                    $this->browserWindowSizeHeight ],

                                                             'enableImages' => $this->browserEnableImages,
                                                             'customFlags'  => [ '--disable-web-security' ],
                                                         ] );

        $this->_createPage();
    }

    /**
     * @throws \HeadlessChromium\Exception\OperationTimedOut
     * @throws \HeadlessChromium\Exception\CommunicationException
     * @throws \HeadlessChromium\Exception\NoResponseAvailable
     */
    protected function _createPage(): void {
        $this->Page = $this->Browser->createPage();
        $this->Page->setUserAgent( $this->userAgentString );
        $this->Page->setCookies( $this->cookies );
    }
}