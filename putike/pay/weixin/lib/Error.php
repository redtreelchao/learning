<?php

namespace Weixin;

class Error
{
	static public function log( $message ) {

	    @file_put_contents( '../log/'.date('Ymd').'error.log', $message, FILE_APPEND ); // LOCK_EX

	}
}
