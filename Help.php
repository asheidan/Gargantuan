<?php

/**
 * Gargantuan/Help.php
 */

namespace Gargantuan;

class Help {
	static function underscore($string) {
		return strtolower(preg_replace('/(?<=\\w)(?=[A-ZÅÄÖ])/','_${1}',$string));
	}

	static function camelcase($string) {
		return str_replace(' ','',ucwords(str_replace('_',' ',$string)));
	}
}
