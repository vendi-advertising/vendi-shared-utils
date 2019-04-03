<?php

use PHPUnit\Framework\TestCase;
use Vendi\Shared;

class test_utils extends TestCase
{
    private $OLD_POST;
    private $OLD_GET;
    private $OLD_COOKIE;
    private $OLD_SERVER;
    private $OLD_SESSION;

    public function setUp() : void
    {
        parent::setUp();

        $this->OLD_COOKIE  = isset( $_COOKIE )  ? $_COOKIE  : null;
        $this->OLD_SERVER  = isset( $_SERVER )  ? $_SERVER  : null;
        $this->OLD_GET     = isset( $_GET )     ? $_GET     : null;
        $this->OLD_POST    = isset( $_POST )    ? $_POST    : null;
        $this->OLD_SESSION = isset( $_SESSION ) ? $_SESSION : null;
    }

    public function tearDown() : void
    {
        $_COOKIE  = $this->OLD_COOKIE;
        $_SERVER  = $this->OLD_SERVER;
        $_GET     = $this->OLD_GET;
        $_POST    = $this->OLD_POST;
        $_SESSION = $this->OLD_SESSION;
    }

    /**
     * @covers Vendi\Shared\utils::reset_all_custom_arrays
     */
    public function test_reset_all_custom_arrays()
    {
        $this->assertNull( \Vendi\Shared\utils::$CUSTOM_GET );
        $this->assertNull( \Vendi\Shared\utils::get_get_value( 'key', null ) );
        \Vendi\Shared\utils::$CUSTOM_GET = array( 'key' => 'value' );
        $this->assertSame( 'value', \Vendi\Shared\utils::get_get_value( 'key', null ) );
        Vendi\Shared\utils::reset_all_custom_arrays();
        $this->assertNull( \Vendi\Shared\utils::get_get_value( 'key', null ) );
    }

    /**
     * @covers Vendi\Shared\utils::is_post
     */
    public function test_is_post()
    {
        $old = \Vendi\Shared\utils::get_server_value( 'REQUEST_METHOD', false );

        $_SERVER[ 'REQUEST_METHOD' ] = 'POST';

        $this->assertTrue( \Vendi\Shared\utils::is_post() );

        if( $old )
        {
            $_SERVER[ 'REQUEST_METHOD' ] = $old;
        }

    }

    /**
     * @covers Vendi\Shared\utils::get_value_multiple_sources
     */
    public function test_get_value_multiple_sources_get_post()
    {
        $_GET[ 'key' ] = 'get';
        $_POST[ 'key' ] = 'post';

        $this->assertSame( 'get', Vendi\Shared\utils::get_value_multiple_sources( 'key', array( 'GET', 'POST' ) ) );
    }

    /**
     * @covers Vendi\Shared\utils::get_value_multiple_sources
     */
    public function test_get_value_multiple_sources_no_get()
    {
        $_GET[ 'key' ] = null;
        $_POST[ 'key' ] = 'post';

        $this->assertSame( 'post', Vendi\Shared\utils::get_value_multiple_sources( 'key', array( 'GET', 'POST' ) ) );
    }

    /**
     * @covers Vendi\Shared\utils::get_value_multiple_sources
     */
    public function test_get_value_multiple_sources_default()
    {
        $_GET[ 'key' ] = 'get';
        $_POST[ 'key' ] = 'post';

        $this->assertSame( 'default', Vendi\Shared\utils::get_value_multiple_sources( 'missing', array( 'GET', 'POST' ), 'default' ) );
    }

    /**
     * @covers Vendi\Shared\utils::is_integer_like
     */
    public function test_utils_is_integer_like()
    {
        $this->assertTrue( \Vendi\Shared\utils::is_integer_like( '1' ) );
        $this->assertTrue( \Vendi\Shared\utils::is_integer_like( '-1' ) );
        $this->assertTrue( \Vendi\Shared\utils::is_integer_like( 1 ) );
        $this->assertTrue( \Vendi\Shared\utils::is_integer_like( -1 ) );
        $this->assertTrue( \Vendi\Shared\utils::is_integer_like( 0 ) );
        $this->assertTrue( \Vendi\Shared\utils::is_integer_like( '999999999999999999999999999999999999999999999999999' ) );

        $this->assertFalse( \Vendi\Shared\utils::is_integer_like( 1.9 ) );
        $this->assertFalse( \Vendi\Shared\utils::is_integer_like( new \stdClass() ) );
        $this->assertFalse( \Vendi\Shared\utils::is_integer_like( array() ) );
        $this->assertFalse( \Vendi\Shared\utils::is_integer_like( false ) );
        $this->assertFalse( \Vendi\Shared\utils::is_integer_like( true ) );
        $this->assertFalse( \Vendi\Shared\utils::is_integer_like( '1.9' ) );
        $this->assertFalse( \Vendi\Shared\utils::is_integer_like( 'cheese' ) );
        $this->assertFalse( \Vendi\Shared\utils::is_integer_like( null ) );
    }

    /**
     * @covers Vendi\Shared\utils::get_request_object
     */
    public function test_custom_arrays_get()
    {
        $this->assertNull( \Vendi\Shared\utils::$CUSTOM_GET );
        $this->assertNull( \Vendi\Shared\utils::get_get_value( 'key', null ) );
        $this->assertArrayNotHasKey( 'key', $_GET );
        \Vendi\Shared\utils::$CUSTOM_GET = array( 'key' => 'value' );
        $this->assertSame( 'value', \Vendi\Shared\utils::get_get_value( 'key' ) );

        \Vendi\Shared\utils::reset_all_custom_arrays();
    }

    /**
     * @covers Vendi\Shared\utils::get_request_object
     */
    public function test_custom_arrays_post()
    {
        $this->assertNull( \Vendi\Shared\utils::$CUSTOM_POST );

        $this->assertNull( \Vendi\Shared\utils::get_post_value( 'key', null ) );
        $this->assertArrayNotHasKey( 'key', $_POST );
        \Vendi\Shared\utils::$CUSTOM_POST = array( 'key' => 'value' );
        $this->assertSame( 'value', \Vendi\Shared\utils::get_post_value( 'key' ) );

        \Vendi\Shared\utils::reset_all_custom_arrays();
    }

    /**
     * @covers Vendi\Shared\utils::get_request_object
     */
    public function test_custom_arrays_cookie()
    {
        $this->assertNull( \Vendi\Shared\utils::$CUSTOM_COOKIE );

        $this->assertNull( \Vendi\Shared\utils::get_cookie_value( 'key', null ) );
        $this->assertArrayNotHasKey( 'key', $_COOKIE );
        \Vendi\Shared\utils::$CUSTOM_COOKIE = array( 'key' => 'value' );
        $this->assertSame( 'value', \Vendi\Shared\utils::get_cookie_value( 'key' ) );

        \Vendi\Shared\utils::reset_all_custom_arrays();
    }

    /**
     * @covers Vendi\Shared\utils::get_request_object
     */
    public function test_custom_arrays_server()
    {
        $this->assertNull( \Vendi\Shared\utils::$CUSTOM_SERVER );

        $this->assertNull( \Vendi\Shared\utils::get_server_value( 'key', null ) );
        $this->assertArrayNotHasKey( 'key', $_SERVER );
        \Vendi\Shared\utils::$CUSTOM_SERVER = array( 'key' => 'value' );
        $this->assertSame( 'value', \Vendi\Shared\utils::get_server_value( 'key' ) );

        \Vendi\Shared\utils::reset_all_custom_arrays();
    }

    /**
     * @covers Vendi\Shared\utils::get_post_value
     * @covers Vendi\Shared\utils::get_request_value
     */
    public function test_utils_get_post_value()
    {
        $_POST[ 'alpha' ] = 'beta';

        $this->assertSame( 'beta', \Vendi\Shared\utils::get_post_value( 'alpha' ) );
        $this->assertSame( 'beta', \Vendi\Shared\utils::get_post_value( 'alpha', 'BAD' ) );
        $this->assertSame( 'alpha', \Vendi\Shared\utils::get_post_value( 'BAD', 'alpha' ) );
    }

    /**
     * @covers Vendi\Shared\utils::get_get_value
     * @covers Vendi\Shared\utils::get_request_value
     */
    public function test_utils_get_get_value()
    {
        $_GET = array( 'alpha' => 'beta' );

        $this->assertSame( 'beta', \Vendi\Shared\utils::get_get_value( 'alpha' ) );
        $this->assertSame( 'beta', \Vendi\Shared\utils::get_get_value( 'alpha', 'BAD' ) );
        $this->assertSame( 'alpha', \Vendi\Shared\utils::get_get_value( 'BAD', 'alpha' ) );
    }

    /**
     * @covers Vendi\Shared\utils::get_server_value
     * @covers Vendi\Shared\utils::get_request_value
     */
    public function test_utils_get_server_value()
    {
        $_SERVER = array( 'alpha' => 'beta' );

        $this->assertSame( 'beta', \Vendi\Shared\utils::get_server_value( 'alpha' ) );
        $this->assertSame( 'beta', \Vendi\Shared\utils::get_server_value( 'alpha', 'BAD' ) );
        $this->assertSame( 'alpha', \Vendi\Shared\utils::get_server_value( 'BAD', 'alpha' ) );
    }

    /**
     * @covers Vendi\Shared\utils::get_cookie_value
     * @covers Vendi\Shared\utils::get_request_value
     */
    public function test_utils_get_cookie_value()
    {
        $_COOKIE = array( 'alpha' => 'beta' );

        $this->assertSame( 'beta', \Vendi\Shared\utils::get_cookie_value( 'alpha' ) );
        $this->assertSame( 'beta', \Vendi\Shared\utils::get_cookie_value( 'alpha', 'BAD' ) );
        $this->assertSame( 'alpha', \Vendi\Shared\utils::get_cookie_value( 'BAD', 'alpha' ) );
    }

    /**
     * @covers Vendi\Shared\utils::get_request_object
     */
    public function test_get_request_object()
    {
        $_POST = array( 'alpha' => 'beta' );
        $_GET = array( 'alpha' => 'beta' );
        $_SERVER = array( 'alpha' => 'beta' );
        $_COOKIE = array( 'alpha' => 'beta' );

        $this->assertSame( $_POST, \Vendi\Shared\utils::get_request_object( 'POST' ) );
        $this->assertSame( $_GET, \Vendi\Shared\utils::get_request_object( 'GET' ) );
        $this->assertSame( $_SERVER, \Vendi\Shared\utils::get_request_object( 'SERVER' ) );
        $this->assertSame( $_COOKIE, \Vendi\Shared\utils::get_request_object( 'COOKIE' ) );

        $this->assertNull( \Vendi\Shared\utils::get_request_object( 'FAKE' ) );
    }

    /**
     * @covers Vendi\Shared\utils::is_request_method
     */
    public function test_is_request_method()
    {
        $_SERVER[ 'REQUEST_METHOD' ] = 'POST';
        $this->assertTrue( \Vendi\Shared\utils::is_request_method( 'POST' ) );

        $_SERVER[ 'REQUEST_METHOD' ] = 'GET';
        $this->assertTrue( \Vendi\Shared\utils::is_request_method( 'GET' ) );

        $_SERVER[ 'REQUEST_METHOD' ] = 'HEAD';
        $this->assertTrue( \Vendi\Shared\utils::is_request_method( 'HEAD' ) );

        $_SERVER[ 'REQUEST_METHOD' ] = 'FAKE';
        $this->assertTrue( \Vendi\Shared\utils::is_request_method( 'FAKE' ) );

        $this->assertFalse( \Vendi\Shared\utils::is_request_method( 'POST' ) );
    }

    /**
     * @covers Vendi\Shared\utils::get_post_value_int
     * @covers Vendi\Shared\utils::get_request_value_int
     */
    public function test_get_post_value_int()
    {
        $_POST = array( 'a' => '1', 'b' => '1.1', 'c' => 'cheese' );

        $this->assertSame( 1, Vendi\Shared\utils::get_post_value_int( 'a' ) );
        $this->assertNull( Vendi\Shared\utils::get_post_value_int( 'b' ) );
        $this->assertNull( Vendi\Shared\utils::get_post_value_int( 'c' ) );
    }

    /**
     * @covers Vendi\Shared\utils::get_get_value_int
     * @covers Vendi\Shared\utils::get_request_value_int
     */
    public function test_get_get_value_int()
    {
        $_GET = array( 'a' => '1', 'b' => '1.1', 'c' => 'cheese' );

        $this->assertSame( 1, Vendi\Shared\utils::get_get_value_int( 'a' ) );
        $this->assertNull( Vendi\Shared\utils::get_get_value_int( 'b' ) );
        $this->assertNull( Vendi\Shared\utils::get_get_value_int( 'c' ) );
    }

    /**
     * @covers Vendi\Shared\utils::get_server_value_int
     * @covers Vendi\Shared\utils::get_request_value_int
     */
    public function test_get_server_value_int()
    {
        $_SERVER = array( 'a' => '1', 'b' => '1.1', 'c' => 'cheese' );

        $this->assertSame( 1, Vendi\Shared\utils::get_server_value_int( 'a' ) );
        $this->assertNull( Vendi\Shared\utils::get_server_value_int( 'b' ) );
        $this->assertNull( Vendi\Shared\utils::get_server_value_int( 'c' ) );
    }

    /**
     * @covers Vendi\Shared\utils::get_cookie_value_int
     * @covers Vendi\Shared\utils::get_request_value_int
     */
    public function test_get_cookie_value_int()
    {
        $_COOKIE = array( 'a' => '1', 'b' => '1.1', 'c' => 'cheese' );

        $this->assertSame( 1, Vendi\Shared\utils::get_cookie_value_int( 'a' ) );
        $this->assertNull( Vendi\Shared\utils::get_cookie_value_int( 'b' ) );
        $this->assertNull( Vendi\Shared\utils::get_cookie_value_int( 'c' ) );
    }
}
