<?php


namespace DPRMC\ComplySciSpider\Tests;


use DPRMC\ComplySciSpider\ComplySciSpider;

if ( !function_exists( 'dd' ) ) {
    function dd() {
        echo '<pre>';
        array_map( function ( $x ) {
            var_dump( $x );
        }, func_get_args() );
        die;
    }
}

class ComplySciSpiderTest extends \PHPUnit\Framework\TestCase {

    const DEBUG = FALSE;

    const TEST_TICKER   = 'AAPL';
    const TEST_TICKER_2 = 'LODE';
    const TEST_VALOREN  = '908440';
    const TEST_CUSIP    = '037833100';


    public static \DPRMC\ComplySciSpider\ComplySciSpider $spider;

    public static function setUpBeforeClass(): void {
        $companyName       = $_ENV[ 'COMPANY_NAME' ];
        $username          = $_ENV[ 'USERNAME' ];
        $password          = $_ENV[ 'PASSWORD' ];
        $chromePath        = $_ENV[ 'CHROME_PATH' ];
        $userAgentString   = $_ENV[ 'USER_AGENT_STRING' ];
        $pathToScreenshots = $_ENV[ 'PATH_TO_SCREENSHOTS' ];

        self::$spider = new \DPRMC\ComplySciSpider\ComplySciSpider( $companyName,
                                                                    $username,
                                                                    $password,
                                                                    $chromePath,
                                                                    $userAgentString,
                                                                    $pathToScreenshots );
    }


    public static function tearDownAfterClass(): void {

    }


    /**
     * @test
     * @group construct
     */
    public function testConstructor() {
        $this->assertInstanceOf( ComplySciSpider::class, self::$spider );
    }


    /**
     * @test
     * @group login
     */
    public function testLoginShouldLogin() {
        self::$spider->enableDebug();
        self::$spider->login();
        $politicalRecipients = self::$spider->requestPoliticalRecipients( NULL, $_ENV[ 'CACHE_FILEPATH' ] );
        $this->assertNotEmpty( $politicalRecipients );
    }


}