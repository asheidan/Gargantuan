<?php

/**
 * Gargantuan/Help.php
 */

namespace Gargantuan;

class Help {
	static function underscore($string) {
		return strtolower(preg_replace('/(?<=\\w)(?=[A-ZÅÄÖ])/','_${1}',$string));
	}
}
