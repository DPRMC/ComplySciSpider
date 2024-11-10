<?php

namespace DPRMC\ComplySciSpider\BrowserHelpers;

use Carbon\Carbon;
use HeadlessChromium\Clip;
use HeadlessChromium\Page;

/**
 *
 */
trait ChromeDebugTrait {

    use BrowserTrait;

    protected bool   $debug;
    protected string $pathToScreenshots;

    public function enableDebug(): void {
        $this->debug = TRUE;
        $this->_debug( "Debug now ENABLED. Screenshots are being saved to: " . $this->pathToScreenshots );
    }

    public function disableDebug(): void {
        $this->_debug( "Debug now DISABLED. Remember to delete screenshots from: " . $this->pathToScreenshots );
        $this->debug = FALSE;
    }


    /**
     * This is just a little helper function to clean up some debug code.
     *
     * @param string                      $suffix
     * @param \HeadlessChromium\Clip|NULL $clip
     *
     * @return void
     * @throws \HeadlessChromium\Exception\CommunicationException
     * @throws \HeadlessChromium\Exception\FilesystemException
     * @throws \HeadlessChromium\Exception\ScreenshotFailed
     */
    public function _screenshot( string $suffix, Clip $clip = NULL ) {
        $now   = Carbon::now( $this->timezone );
        $time  = $now->timestamp;
        $micro = $now->microsecond;

        if ( $this->debug ):
            if ( $clip ):
                $this->Page->screenshot( [ 'clip' => $clip ] )
                           ->saveToFile( $this->pathToScreenshots . $time . '_' . $micro . '_' . $suffix . '.jpg' );
            else:
                $this->Page->screenshot()
                           ->saveToFile( $this->pathToScreenshots . $time . '_' . $micro . '_' . $suffix . '.jpg' );
            endif;
        endif;
    }


    public function _html( string $filename ) {
        $now   = Carbon::now( $this->timezone );
        $time  = $now->timestamp;
        $micro = $now->microsecond;
        if ( $this->debug ):
            $html = $this->Page->getHtml();
            file_put_contents( $this->pathToScreenshots . $time . '_' . $micro . '_' . $filename . '.html', $html );
        endif;
    }

    public function _debug( string $message, bool $die = FALSE ) {
        if ( $this->debug ):
            echo "\n" . $message;
            flush();
            if ( $die ):
                die();
            endif;
        endif;
    }
}