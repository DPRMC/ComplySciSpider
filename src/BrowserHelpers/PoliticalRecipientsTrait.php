<?php

namespace DPRMC\ComplySciSpider\BrowserHelpers;


use DOMDocument;
use HeadlessChromium\Clip;
use HeadlessChromium\Page;

/**
 *
 */
trait PoliticalRecipientsTrait {

    use BrowserTrait;
    use ChromeDebugTrait;

    protected string $cacheFilePrefix = 'political_recipients_';
    protected string $cacheFileType   = '.json';

    protected array $headers = [
        'recipient_name',
        'year',
        'position',
        'level',
        'jurisdiction',
        'last_modified',
        'created_by',
        'created_date',
        'modified_by',
        'custom_recipient',
    ];


    /**
     * @param int|NULL    $limit
     * @param string|null $cacheLocation An absolute filepath that ends with a directory separator.
     *
     * @return array
     * @throws \HeadlessChromium\Exception\CommunicationException
     * @throws \HeadlessChromium\Exception\CommunicationException\CannotReadResponse
     * @throws \HeadlessChromium\Exception\CommunicationException\InvalidResponse
     * @throws \HeadlessChromium\Exception\CommunicationException\ResponseHasError
     * @throws \HeadlessChromium\Exception\ElementNotFoundException
     * @throws \HeadlessChromium\Exception\FilesystemException
     * @throws \HeadlessChromium\Exception\JavascriptException
     * @throws \HeadlessChromium\Exception\NavigationExpired
     * @throws \HeadlessChromium\Exception\NoResponseAvailable
     * @throws \HeadlessChromium\Exception\OperationTimedOut
     * @throws \HeadlessChromium\Exception\ScreenshotFailed
     */
    public function requestPoliticalRecipients( int $limit = NULL, string $cacheLocation = NULL ): array {
        $politicalRecipients = [];
        $url                 = 'https://' . $this->companyName . '.complysci.com/UserData/PoliticalRecipients';
        //$jsonUrl = 'https://' . $this->companyName . '.complysci.com/UserData/GetCompanyPoliticalRecipientsGridJson';
        $this->Page->navigate( $url )->waitForNavigation( Page::NETWORK_IDLE );
        $this->_screenshot( 'political_recipients' );
        $this->_html( 'political_recipients' );

        $previousHtml = '';
        $html         = $this->Page->getHtml();

        $newPoliticalRecipients = $this->_parsePoliticalRecipientsFromHtml( $html );
        $politicalRecipients    = array_merge( $politicalRecipients, $newPoliticalRecipients );

        $numIterations = 0;
        while ( $html != $previousHtml ):
            $numIterations++;
            $previousHtml = $html;
            $html         = $this->_getHtmlFromNextPage();
            $this->_screenshot( 'political_recipients_page_' . $numIterations );
            $this->_html( 'political_recipients_page_' . $numIterations );
            $newPoliticalRecipients = $this->_parsePoliticalRecipientsFromHtml( $html );
            $politicalRecipients    = array_merge( $politicalRecipients, $newPoliticalRecipients );

            $this->_debug( "Political Recipients Page: " . $numIterations . " found " . count( $newPoliticalRecipients ) . " for a total of " . count( $politicalRecipients ) . " political recipients." );

            if ( $cacheLocation ) :
                $cacheFileName = $cacheLocation . $this->cacheFilePrefix . $numIterations . $this->cacheFileType;
                $bytes         = file_put_contents( $cacheFileName,
                                                    json_encode( $newPoliticalRecipients ) );
                $this->_debug( "Cache file " . $cacheFileName . " written with " . $bytes . " bytes." );
            endif;

            if ( $limit && count( $politicalRecipients ) > $limit ):
                return $politicalRecipients;
            endif;
        endwhile;

        return $politicalRecipients;

        //$this->Page->mouse()->find( "a[title='Go to the next page'] span" )->click();

        //$position = $this->Page->mouse()->getPosition();
        //$mouseX   = $position[ 'x' ];
        //$mouseY   = $position[ 'y' ];

        //print_r( $position );
        //$mouseClip = new Clip( $mouseX - 100, $mouseY - 100, 200, 200 );
        //$this->_screenshot( 'mouse_position', $mouseClip );


        //sleep( 3 );

        //$this->_screenshot( 'political_recipients_page_' );
        //$this->_html( 'political_recipients_page_' );


        //$this->Page->navigate( $jsonUrl )->waitForNavigation( Page::NETWORK_IDLE );
        //$this->Debug->_html('political_recipients_json' );
        //$json = $this->Page->getHtml();
        //$politicalRecipients = json_decode( $json, true );
        //echo "POLITICAL RECIPIENTS:\n";
        //print_r($politicalRecipients);
        //die();
        ///**
        // * @var CookiesCollection $cookiesCollection
        // */
        //$cookiesCollection = $this->Page->getAllCookies();
        //$cookieString      = '';
        ///**
        // * @var \HeadlessChromium\Cookies\Cookie $cookie
        // */
        //foreach ( $cookiesCollection as $cookie ):
        //    $cookieString .= $cookie->getName() . '=' . $cookie->getValue() . '; ';
        //endforeach;
        //$cookieString = substr( $cookieString, 0, -2 );
        //echo "\ncookieString\n";
        //echo "\n$cookieString\n";
        //$headers      = [
        //    'Accept'             => '*/*',
        //    'Accept-Language'    => 'en-US,en;q=0.9',
        //    'Connection'         => 'keep-alive',
        //    'Content-Type'       => 'application/x-www-form-urlencoded; charset=UTF-8',
        //    'Cookie'             => $cookieString,
        //    'DNT'                => '1',
        //    'Origin'             => 'https://' . $this->companyName . '.complysci.com',
        //    'Referer'            => 'https://' . $this->companyName . '.complysci.com/UserData/PoliticalRecipients',
        //    'Sec-Fetch-Dest'     => 'empty',
        //    'Sec-Fetch-Mode'     => 'cors',
        //    'Sec-Fetch-Site'     => 'same-origin',
        //    'User-Agent'         => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Safari/537.36',
        //    'X-Requested-With'   => 'XMLHttpRequest',
        //    'sec-ch-ua'          => '"Chromium";v="130", "Google Chrome";v="130", "Not?A_Brand";v="99"',
        //    'sec-ch-ua-mobile'   => '?0',
        //    'sec-ch-ua-platform' => '"macOS"',
        //];
        //$options      = [
        //    'headers'   => $headers,
        //    'form_data' => [
        //        'page'     => 1,
        //        'pageSize' => 100,
        //    ],
        //];
        //
        //try {
        //    $response = $this->client->post( $jsonUrl, $options );
        //    $json     = $response->getBody()->getContents();
        //
        //    print_r( $json );
        //    echo "\nABOVE IS THE JSON\n";
        //    //$politicalRecipients = json_decode( $json, TRUE );
        //    //echo "POLITICAL RECIPIENTS:\n";
        //    //print_r( $politicalRecipients );
        //
        //
        //    die();
        //} catch ( \Exception $exception ) {
        //
        //    print_r( $exception->getResponse()->getBody()->getContents() );
        //    flush();
        //    echo get_class( $exception ) . ': ' . $exception->getMessage();
        //    die('this is the end.');
        //}


        //
        ////print_r( $this->Page->getCookies() );
        ////print_r( $this->Page->getAllCookies() );
        //
        //// curl 'https://deerparkrd.complysci.com/UserData/GetCompanyPoliticalRecipientsGridJson' \
        ////  -H 'Accept: */*' \
        ////  -H 'Accept-Language: en-US,en;q=0.9' \
        ////  -H 'Connection: keep-alive' \
        ////  -H 'Content-Type: application/x-www-form-urlencoded; charset=UTF-8' \
        ////  -H 'Cookie: MyCustomAuthentication=; ASP.NET_SessionId=2p22h02dkr0blqdpjmfsros4; __RequestVerificationToken=dU0ypzwZmIxhmTQWGoFBErPkQj37LIGtlWLAAiioreZs2iW0KGPZc-vPbmTOu9kLYxsuP21DrZwpB-P3r6TDIGPeSWK3QDcBUJXFPXb-y9c1; FedAuth=77u/PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz48U2VjdXJpdHlDb250ZXh0VG9rZW4gcDE6SWQ9Il8yYWI4MGFlOS0zNTM1LTQxMzAtYjE4ZC01MDlhMmMxZjE4MGYtM0Y2RjM3RDU0MkM3NkVCQjg2OTZEN0VDQzJCMzhBMDkiIHhtbG5zOnAxPSJodHRwOi8vZG9jcy5vYXNpcy1vcGVuLm9yZy93c3MvMjAwNC8wMS9vYXNpcy0yMDA0MDEtd3NzLXdzc2VjdXJpdHktdXRpbGl0eS0xLjAueHNkIiB4bWxucz0iaHR0cDovL2RvY3Mub2FzaXMtb3Blbi5vcmcvd3Mtc3gvd3Mtc2VjdXJlY29udmVyc2F0aW9uLzIwMDUxMiI+PElkZW50aWZpZXI+dXJuOnV1aWQ6NGU1MWY5MDEtNTMzMC00NTNjLWFjYTktZTU5MDEyZjUyMTE5PC9JZGVudGlmaWVyPjwvU2VjdXJpdHlDb250ZXh0VG9rZW4+' \
        ////  -H 'DNT: 1' \
        ////  -H 'Origin: https://deerparkrd.complysci.com' \
        ////  -H 'Referer: https://deerparkrd.complysci.com/UserData/PoliticalRecipients' \
        ////  -H 'Sec-Fetch-Dest: empty' \
        ////  -H 'Sec-Fetch-Mode: cors' \
        ////  -H 'Sec-Fetch-Site: same-origin' \
        ////  -H 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Safari/537.36' \
        ////  -H 'X-Requested-With: XMLHttpRequest' \
        ////  -H 'sec-ch-ua: "Chromium";v="130", "Google Chrome";v="130", "Not?A_Brand";v="99"' \
        ////  -H 'sec-ch-ua-mobile: ?0' \
        ////  -H 'sec-ch-ua-platform: "macOS"' \
        ////  --data-raw 'sort=&page=1&pageSize=100&group=&filter='
        //
        ////$cookieString = 'MyCustomAuthentication=; ASP.NET_SessionId=2p22h02dkr0blqdpjmfsros4; __RequestVerificationToken=dU0ypzwZmIxhmTQWGoFBErPkQj37LIGtlWLAAiioreZs2iW0KGPZc-vPbmTOu9kLYxsuP21DrZwpB-P3r6TDIGPeSWK3QDcBUJXFPXb-y9c1; FedAuth=77u/PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz48U2VjdXJpdHlDb250ZXh0VG9rZW4gcDE6SWQ9Il8yYWI4MGFlOS0zNTM1LTQxMzAtYjE4ZC01MDlhMmMxZjE4MGYtM0Y2RjM3RDU0MkM3NkVCQjg2OTZEN0VDQzJCMzhBMDkiIHhtbG5zOnAxPSJodHRwOi8vZG9jcy5vYXNpcy1vcGVuLm9yZy93c3MvMjAwNC8wMS9vYXNpcy0yMDA0MDEtd3NzLXdzc2VjdXJpdHktdXRpbGl0eS0xLjAueHNkIiB4bWxucz0iaHR0cDovL2RvY3Mub2FzaXMtb3Blbi5vcmcvd3Mtc3gvd3Mtc2VjdXJlY29udmVyc2F0aW9uLzIwMDUxMiI+PElkZW50aWZpZXI+dXJuOnV1aWQ6NGU1MWY5MDEtNTMzMC00NTNjLWFjYTktZTU5MDEyZjUyMTE5PC9JZGVudGlmaWVyPjwvU2VjdXJpdHlDb250ZXh0VG9rZW4+';
        ////
        //$jsonUrl = 'https://deerparkrd.complysci.com/UserData/GetCompanyPoliticalRecipientsGridJson';
        //$headers = [
        //    'Accept'             => '*/*',
        //    'Accept-Language'    => 'en-US,en;q=0.9',
        //    'Connection'         => 'keep-alive',
        //    'Content-Type'       => 'application/x-www-form-urlencoded; charset=UTF-8',
        //    'Cookie'             => $cookieString,
        //    'DNT'                => '1',
        //    'Origin'             => 'https://deerparkrd.complysci.com',
        //    'Referer'            => 'https://deerparkrd.complysci.com/UserData/PoliticalRecipients',
        //    'Sec-Fetch-Dest'     => 'empty',
        //    'Sec-Fetch-Mode'     => 'cors',
        //    'Sec-Fetch-Site'     => 'same-origin',
        //    'User-Agent'         => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Safari/537.36',
        //    'X-Requested-With'   => 'XMLHttpRequest',
        //    'sec-ch-ua'          => '"Chromium";v="130", "Google Chrome";v="130", "Not?A_Brand";v="99"',
        //    'sec-ch-ua-mobile'   => '?0',
        //    'sec-ch-ua-platform' => '"macOS"',
        //];
        //$options = [
        //    'headers' => $headers,
        //];
        //
        //try {
        //    $response = $this->client->post( $url, $options );
        //    print_r( $response->getBody()->getContents() );
        //    die();
        //} catch ( \Exception $exception ) {
        //    print_r( $exception->getResponse()->getBody()->getContents() );
        //}
        //

        //$this->Page->getSession()->on('method:Network.responseReceived', function (array $params): void {
        //    print_r($params);
        //});


//        $this->handler = function ( array $params ): void {
//
////            print_r($params);
////die();
//
//            if ( $params[ 'response' ][ 'url' ] != 'https://deerparkrd.complysci.com/UserData/PoliticalRecipients' ):
//                return;
//            endif;
//
//            $request_id = $params[ 'requestId' ];
//            print_r($params);
//            print_r( $request_id );
//            $data = $this->Page->getSession()->sendMessageSync( new \HeadlessChromium\Communication\Message( 'Network.getResponseBody', [ 'requestId' => $request_id ] ) )->getData();
//
//            print_r( $data );
//
//
//            //die();
//            //$url = @$params["response"]["url"];
//            //
//            //if (strpos($url, "PATH_TO_FILE") !== false)
//            //{
//            //
//            //    $this->Page->getSession()->removeListener('method:Network.responseReceived', $this->handler);
//            //
//            //    //$request_id         = @$params["requestId"];
//            //    //$data               = @$this->Page->getSession()->sendMessageSync(new \HeadlessChromium\Communication\Message('Network.getResponseBody', ['requestId' => $request_id]))->getData();
//            //
//            //    $request_id         = $params["requestId"];
//            //    $data               = $this->Page->getSession()->sendMessageSync(new \HeadlessChromium\Communication\Message('Network.getResponseBody', ['requestId' => $request_id]))->getData();
//            //
//            //    echo 'this is dataaaaa';
//            //    print_r($data);
//            //
//            //    //CONTENT OF FILE
//            //    $content            = $data["result"]["body"];
//            //    print_r($content);
//            //}
//        };
//
//
//        $this->Page->getSession()->on( 'method:Network.responseReceived', $this->handler );
//
//
//        $this->Page->navigate( $url )->waitForNavigation();
//
//
//        die();

        //$this->Page->getSession()->on( 'method:Network.requestIntercepted', function ( array $params ): void {
        //    if (
        //        (strstr( $params[ "request" ][ "url" ], "google.com" ) &&
        //         (
        //             $params[ "resourceType" ] == "Document" ||
        //             $params[ "resourceType" ] == "Script"
        //         )
        //        )
        //    ) {
        //
        //        echo "OK: " . $params[ "resourceType" ] . " - " . $params[ "request" ][ "url" ] . PHP_EOL;
        //
        //        // allow request to go through
        //        $this->Page->getSession()->sendMessage(
        //            new Message( 'Network.continueInterceptedRequest', [ 'interceptionId' => $params[ "interceptionId" ] ] )
        //        );
        //    } else {
        //
        //        echo "BLOCKED: " . $params[ "resourceType" ] . " - " . $params[ "request" ][ "url" ] . PHP_EOL;
        //
        //        // block request
        //        $this->Page->getSession()->sendMessage(
        //            new Message( 'Network.continueInterceptedRequest', [ 'interceptionId' => $params[ "interceptionId" ], 'errorReason' => 'BlockedByClient' ] )
        //        );
        //
        //    }
        //} );


        //$this->Page->getSession()->on( 'method:Network.requestIntercepted', function ( array $params ): void {
        //    echo "OK: " . $params[ "resourceType" ] . " - " . $params[ "request" ][ "url" ] . PHP_EOL;
        //
        //    // allow request to go through
        //    $this->Page->getSession()->sendMessage(
        //        new Message( 'Network.continueInterceptedRequest', [ 'interceptionId' => $params[ "interceptionId" ] ] )
        //    );
        //} );
        //
        //// intercept all requests
        //$thing = $this->Page->getSession()->sendMessage( new Message( 'Network.setRequestInterception', [ 'patterns' => [ [ 'urlPattern' => '*' ] ] ] ) );
        //
        //
        //print_r( $thing );

        //$this->Page->getFrameManager()->getMainFrame()->

        //$html = $this->Page->getHtml();
        //print_r($html);
    }


    /**
     * @param string $html
     *
     * @return array
     */
    public function _parsePoliticalRecipientsFromHtml( string $html ): array {
        $politicalRecipients = [];

        $dom = new \DOMDocument();
        @$dom->loadHTML( $html );

        $tbodies   = $dom->getElementsByTagName( 'tbody' );
        $onlyTbody = $tbodies->item( 0 );
        $trs       = $onlyTbody->getElementsByTagName( 'tr' );
        foreach ( $trs as $tr ):
            $newPoliticalRecipient = [];
            $tds                   = $tr->getElementsByTagName( 'td' );
            foreach ( $tds as $i => $td ):
                $newPoliticalRecipient[ $this->headers[ $i ] ] = trim( $td->textContent );
            endforeach;
            $politicalRecipients[] = $newPoliticalRecipient;
        endforeach;


        return $politicalRecipients;
    }


    /**
     * @return string
     * @throws \HeadlessChromium\Exception\CommunicationException
     * @throws \HeadlessChromium\Exception\ElementNotFoundException
     * @throws \HeadlessChromium\Exception\JavascriptException
     * @throws \HeadlessChromium\Exception\NoResponseAvailable
     */
    protected function _getHtmlFromNextPage(): string {
        $this->Page->mouse()->find( "a[title='Go to the next page'] span" )->click();
        $sleep = rand( 6, 8 );
        $this->_debug( "Sleeping $sleep seconds..." );
        sleep( $sleep );
        return $this->Page->getHtml();
    }

}