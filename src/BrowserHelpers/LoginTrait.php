<?php

namespace DPRMC\ComplySciSpider\BrowserHelpers;

use HeadlessChromium\Clip;
use HeadlessChromium\Exception\OperationTimedOut;
use HeadlessChromium\Page;

/**
 *
 */
trait LoginTrait {

    use BrowserTrait;
    use ChromeDebugTrait;

    protected int $logInButtonX = 800;
    protected int $logInButtonY = 500;

    protected string $loginUrl;


    /**
     * @return string
     * @throws \HeadlessChromium\Exception\CommunicationException
     * @throws \HeadlessChromium\Exception\CommunicationException\CannotReadResponse
     * @throws \HeadlessChromium\Exception\CommunicationException\InvalidResponse
     * @throws \HeadlessChromium\Exception\CommunicationException\ResponseHasError
     * @throws \HeadlessChromium\Exception\FilesystemException
     * @throws \HeadlessChromium\Exception\NavigationExpired
     * @throws \HeadlessChromium\Exception\NoResponseAvailable
     * @throws \HeadlessChromium\Exception\OperationTimedOut
     * @throws \HeadlessChromium\Exception\ScreenshotFailed
     * @throws \Exception
     */
    public function login(): string {

        $this->loginUrl = 'https://' . $this->companyName . '.complysci.com/Membership/Login';

        $this->_debug( "Navigating to login screen: " . $this->loginUrl );
        $this->Page->navigate( $this->loginUrl )->waitForNavigation(Page::NETWORK_IDLE);
        $this->_screenshot( 'first_page' );
        $this->_html( 'first_page' );

        $this->_debug( "Filling out user and pass." );
        $this->Page->evaluate( "document.querySelector('#UserName').value = '" . $this->username . "';" );
        $this->Page->evaluate( "document.querySelector('#Password').value = '" . $this->password . "';" );

        // DEBUG
        $this->_screenshot( 'filled_in_user_pass' );
        $this->_screenshot( 'where_i_will_click_to_login', new Clip( 0,
                                                                         0,
                                                                         $this->logInButtonX,
                                                                         $this->logInButtonY ) );

        try {
            $this->Page->mouse()
                       ->move( $this->logInButtonX, $this->logInButtonY )
                       ->click();

            $this->Page->waitForReload();

            //sleep( 2 );
            $this->_screenshot( 'should be logged in' );


        } catch ( OperationTimedOut $exception ) {
            $this->_screenshot( 'i_am_not_logged_in' );
            $this->_html( 'i_am_not_logged_in' );

            $html = $this->Page->getHtml();
            //if ( $this->_needToResetPassword( $html ) ):
            //    throw new NeedToResetPasswordException();
            //endif;
            //
            //throw new LoginTimedOutException( $exception->getMessage(),
            //                                  $exception->getCode(),
            //                                  $exception );
        }


        $this->_screenshot( 'am_i_logged_in' );

        $this->cookies = $this->Page->getAllCookies();
        $postLoginHTML = $this->Page->getHtml();

        $this->_html( "post_login" );

        return $postLoginHTML;
    }



}