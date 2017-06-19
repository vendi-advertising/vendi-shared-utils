<?php

use PHPUnit\Framework\TestCase;
use Vendi\Shared\fs_utils;

class test_fs_utils extends TestCase
{
    private $_folders_to_delete = [];

    private static function _delTree( $dir )
    {
        $files = array_diff( scandir( $dir ), array( '.', '..' ) );
        foreach ($files as $file)
        {
            if( is_file("$dir/$file") )
            {
                throw new \Exception( 'Test completed successfully but could not clean up one or more directories because it contained files.' );
            }

            self::_delTree("$dir/$file");
        }
        return rmdir( $dir );
  }

    public function tearDown()
    {
        parent::tearDown();

        $error = false;

        foreach( $this->_folders_to_delete as $dir )
        {
            if( ! self::_delTree( $dir ) )
            {
                $error = true;
            }
        }

        if( $error )
        {
            throw new \Exception( 'Test completed successfully but could not clean up one or more directories.' );
        }
    }
    /**
     * @covers Vendi\Shared\fs_utils::create_random_temp_dir
     * @expectedException Vendi\Shared\utils_exception
     */
    public function test_create_random_temp_dir__no_prefix()
    {
        fs_utils::create_random_temp_dir( '' );
    }

    /**
     * @covers Vendi\Shared\fs_utils::create_random_temp_dir
     */
    public function test_create_random_temp_dir__normal()
    {
        $dir = fs_utils::create_random_temp_dir( 'TESTING' );

        $this->assertTrue( is_dir( $dir ) );

        $this->_folders_to_delete[] = $dir;
    }

    /**
     * @covers Vendi\Shared\fs_utils::create_random_temp_dir
     */
    public function test_create_random_temp_dir__loop()
    {
        //NOTE: This is weird so stay with me.
        //The second parameter to create_random_temp_dir() is an optional
        //callable method to create the "unique" subfolder. If not supplied then
        //the built-in uniqid is used. The method create_random_temp_dir() will
        //loop until it generated a directory that does not exist yet. The
        //likelihook of uniqid() generating the same folder twice is essentially
        //zero so for testing purposes we're going to have a "fake unique
        //generator" which will actually return existing directories a couple of
        //times. In order for that to work, however, we first need to create a
        //directory that doesn't already exist!
        $tmp_folder = sys_get_temp_dir();
        $full_path = null;
        while( true )
        {
            $sub_folder = uniqid( 'UNIT_TESTING', true );
            $full_path = fs_utils::combine_paths( fs_utils::OK_IF_PATH_DOES_NOT_EXIST, $tmp_folder, $sub_folder );
            if( ! is_dir( $full_path ) )
            {
                break;
            }
        }

        //The above found a unique folder that doesn't exist in %TMP%, make it.
        mkdir( $full_path );

        //We're going to return the sub folder a couple of times when asked
        //before asking PHP to generate a unique folder for us. Setup a counter
        //so that we know how many times we've looped.
        $counter = 0;

        //This function will be called by create_random_temp_dir().
        //NOTE: $counter must be used as a reference otherwise it will receive a
        //copy and an infinite loop will happen.
        $func = function( $prefix ) use ( &$counter, $sub_folder )
                {
                    if( $counter < 2 )
                    {
                        $counter++;

                        //Return a folder that we know exists
                        return $sub_folder;
                    }

                    //Return a truly unique folder
                    return uniqid( $prefix, true );

                };

        $dir = fs_utils::create_random_temp_dir( 'TESTING', $func );

        $this->assertTrue( is_dir( $dir ) );

        $this->_folders_to_delete[] = $dir;
        $this->_folders_to_delete[] = $full_path;
    }

    /**
     * @covers Vendi\Shared\fs_utils::mkdir
     * @expectedException Vendi\Shared\utils_exception
     */
    public function test_mkdir__empty()
    {
        fs_utils::mkdir( '' );
    }

    /**
     * @covers Vendi\Shared\fs_utils::mkdir
     * @expectedException Vendi\Shared\utils_exception
     */
    public function test_mkdir__whitespace()
    {
        fs_utils::mkdir( ' ' );
    }

    /**
     * @covers Vendi\Shared\fs_utils::mkdir
     * @expectedException Vendi\Shared\utils_exception
     */
    public function test_mkdir__not_abs()
    {
        fs_utils::mkdir( 'cheese' );
    }

    /**
     * @covers Vendi\Shared\fs_utils::mkdir
     * @expectedException Vendi\Shared\utils_exception
     * @expectedExceptionMessage Cannot create directory /cheese.
     */
    public function test_mkdir__cannot_create()
    {
        fs_utils::mkdir( '/cheese' );
    }

    /**
     * @covers Vendi\Shared\fs_utils::mkdir
     * @expectedException Vendi\Shared\utils_exception
     * @expectedExceptionMessage Cannot create directory /cheese for context testing.
     */
    public function test_mkdir__cannot_create_with_context()
    {
        fs_utils::mkdir( '/cheese', 'testing' );
    }

    /**
     * @covers Vendi\Shared\fs_utils::mkdir
     */
    public function test_mkdir__exists()
    {
        $this->assertTrue( fs_utils::mkdir( sys_get_temp_dir() ) );
    }


    /**
     * @covers Vendi\Shared\fs_utils::mkdir
     */
    public function test_mkdir__normal()
    {
        $full_path = null;

        $tmp_folder = sys_get_temp_dir();
        while( true )
        {
            $sub_folder = uniqid( 'UNIT_TESTING', true );
            $full_path = fs_utils::combine_paths( fs_utils::OK_IF_PATH_DOES_NOT_EXIST, $tmp_folder, $sub_folder );
            if( ! is_dir( $full_path ) )
            {
                break;
            }
        }

        $this->assertTrue( fs_utils::mkdir( $full_path ) );

        $this->_folders_to_delete[] = $full_path;
    }

    /**
     * @covers Vendi\Shared\fs_utils::combine_paths_with_file
     * @dataProvider _provider_for__combine_paths_with_file
     */
    public function test_combine_paths_with_file( string $expected, bool $create, string $file, array $path_parts )
    {
        $this->assertSame( $expected, fs_utils::combine_paths_with_file( $create, $file, ...$path_parts ) );
        if( $create )
        {
            $this->_folders_to_delete[] = dirname( $expected );
        }
    }

    /**
     * @covers Vendi\Shared\fs_utils::combine_paths
     * @dataProvider _provider_for__combine_paths_with_file
     */
    public function test_combine_paths( string $expected, bool $create, string $file, array $path_parts )
    {
        $expected = rtrim( dirname( $expected ), '/' ) . '/';

        $this->assertSame( $expected, fs_utils::combine_paths( $create, ...$path_parts ) );
        if( $create )
        {
            //We can't delete the root tmp folder
            if( '/tmp' !== dirname( $expected ) )
            {
                $this->_folders_to_delete[] = dirname( $expected );
            }
        }
    }

    public function _provider_for__combine_paths_with_file()
    {
        return [
                    //Various combinations, don't create
                    [ '/test.json',                  false, 'test.json', [  ] ],
                    [ '/tmp/test.json',              false, 'test.json', [ 'tmp' ] ],
                    [ '/tmp/cheese/test.json',       false, 'test.json', [ 'tmp', 'cheese' ] ],
                    [ '/tmp/cheese/alpha/test.json', false, 'test.json', [ 'tmp', 'cheese', 'alpha' ] ],

                    //Throw some slashes in there which should be handled
                    [ '/tmp/cheese/test.json',       false, 'test.json', [ '/tmp/', '/cheese/' ] ],

                    //Start creating paths
                    [ '/tmp/cheese/test.json',       true, 'test.json', [ 'tmp', 'cheese' ] ],

                    //Extra deep path
                    [ '/tmp/cheese/alpha/test.json', true, 'test.json', [ 'tmp', 'cheese', 'alpha' ] ],
            ];
    }
}
