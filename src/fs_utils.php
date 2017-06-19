<?php

namespace Vendi\Shared;

final class fs_utils
{

    const CREATE_DIRECTORY_IF_NOT_EXISTS_OR_FAIL = true;

    const OK_IF_PATH_DOES_NOT_EXIST = false;

    /**
     * [create_random_temp_dir description]
     * @param  string        $prefix    [description]
     * @param  callable|null $rand_func [description]
     * @return [type]                   [description]
     */
    public static function create_random_temp_dir( string $prefix, callable $rand_func = null ) : string
    {
        if( ! $prefix )
        {
            throw new utils_exception( 'You must provide a prefix when calling create_random_temp_dir()' );
        }

        $prefix = trim( $prefix, '_' ) . '_';

        if( ! $rand_func )
        {
            $rand_func = function( $prefix )
                         {
                            return uniqid( $prefix, true );
                        };
        }

        $tmp_folder = sys_get_temp_dir();
        while( true )
        {
            $sub_folder = $rand_func( $prefix );

            //Normally we'd want combine_paths to make the directory for us but
            //in this case we only want to return if it doesn't already exist.
            $full_path = self::combine_paths( self::OK_IF_PATH_DOES_NOT_EXIST, $tmp_folder, $sub_folder );

            //The directory exists, loop again
            if( is_dir( $full_path ) )
            {
                continue;
            }

            //Make the directory.
            //NOTE: This method throws if it cannot create the directory.
            self::mkdir( $full_path );

            break;
        }

        //Return this outside of the while loop so that code coverage can handle
        //the full function
        return $full_path;
    }

    public static function combine_paths_with_file( bool $create, string $file, string ...$path_parts )
    {
        $ret = '/';

        foreach( $path_parts as $pp )
        {
            $ret .= trim( $pp, '/\\' ) . '/';

            if( $create )
            {
                self::mkdir( $ret );
            }
        }

        if( $file )
        {
            $ret .= trim( $file, '/\\' );
        }

        return $ret;
    }

    public static function combine_paths( bool $create, string ...$path_parts )
    {
        return self::combine_paths_with_file( $create, '', ...$path_parts );
    }

    /**
     * Create the given path if it doesn't exist.
     *
     * NOTE: This method does not test user permissions to write to the
     * directory, that must be handled by callers.
     *
     * @param  string $path    The absolute path
     * @param  string $context An optional context to help explain the mkdir
     *                         conditions for debugging.
     * @return true            Returns true if successful, otherwise an
     *                         exception is thrown.
     */
    public static function mkdir( string $path, string $context = null ) : bool
    {
        //Make sure we've got something to work with.
        if( ! $path || ! trim( $path ) )
        {
            throw new utils_exception( 'You must provide a path when calling mkdir.' );
        }

        //Make sure the path is absolute
        if( 0 !== strpos( $path, '/' ) )
        {
            throw new utils_exception( 'You must provide an absolute path when calling mkdir.' );
        }

        //Does the directory already exist?
        if( is_dir( $path ) )
        {
            return true;
        }

        //Try to make it
        @mkdir( $path );

        //mkdir fails if the directory already exists however if this
        //command is executed simultaneously by two people there could be a
        //race condition. So before failing hard, see if someone else
        //actually succeeded in making in.
        if( is_dir( $path ) )
        {
            return true;
        }

        if( $context )
        {
            $msg = sprintf( 'Cannot create directory %1$s for context %2$s.', $path, $context );
        }
        else
        {
            $msg = sprintf( 'Cannot create directory %1$s.', $path, $context );
        }

        throw new utils_exception( $msg );
    }
}
