# Utility class generally for HTTP.

[![Build Status](https://travis-ci.org/vendi-advertising/vendi-shared-utils.svg?branch=master)](https://travis-ci.org/vendi-advertising/vendi-shared-utils)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/vendi-advertising/vendi-shared-utils/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/vendi-advertising/vendi-shared-utils/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/vendi-advertising/vendi-shared-utils/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/vendi-advertising/vendi-shared-utils/?branch=master)
[![codecov](https://codecov.io/gh/vendi-advertising/vendi-shared-utils/branch/master/graph/badge.svg)](https://codecov.io/gh/vendi-advertising/vendi-shared-utils)

NOTE: Do not modify any methods in this class ever. You can add new methods as needed but there is a lot of code the depends on this functioning in a specific fashion and since this is a shared class you are not guarenteed to have this specific class actually loaded.

To clarify the above, this class is intended to be used by multiple plugins and there is no guarantee of load order. If you add a new method to this class you should grep the server for other installs and add code to those, too, since you don't know if your code will load first.

Any additional methods to this class MUST work without fail and can have zero dependencies upon other code.

## History:

### 4.0.0
 - Bumped PHP minimum requirement to 7.0
 - Added `fs_utils`:
   - `create_random_temp_dir`
   - `combine_paths_with_file`
   - `combine_paths`
   - `mkdir`


### 3.1.0
 - Remove `unparse_url()` which was from a different project.
 - Finished documenting everything.

### 3.0.5
 - Added `get_value_multiple_sources()`.
 - Fixed documentation errors from copy and pasting.

### 3.0.4
 - Only trim() if the value is a string.

### 2.1.0
 - Allow setting custom POST/GET/COOKIE/SERVER on static fields of class. If set they will be used in place of the global $_ XYZ values. Also added `reset_all_custom_arrays()` to erase these values. These changes should be 100% backwards compatible.

### 2.0.1
 - Added `unparse_url()`

### 2.0.0
 - Rewrite of previous code into this namesapce
