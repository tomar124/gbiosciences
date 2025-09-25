<?php

namespace Journal3\Options;

class Image extends Option {

	protected static function parseValue($value, $data = null) {
		
            if (static::$s3 && $value) {
                if (static::$s3->headObject(ltrim(str_replace(['//', '///', '////'], '', $value), '/'), '')) {
                    return $value;
                }
		    } elseif (is_file(DIR_IMAGE . $value)) {
            
			return $value;
		}

		return null;
	}

}
