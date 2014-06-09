<?php

$apiConfig = array(
		'site_name' => 'https://people.rit.edu/nmr9601/sandbox',

		'oauth2_client_id' => '14798831816-798jk1fcv8qjfhbq9ci844fdsckj0euv.apps.googleusercontent.com',
		'oauth2_client_secret' => 'w9271HiaQnHLx99nRyU_QMqU',
		'oauth2_redirect_uri' => 'https://people.rit.edu/nmr9601/sandbox',

		//'developer_key' => '',

		'authClass' => 'apiOAuth2',

		'services' => array(
			'calendar' => array('scope'=>'https://googleapis.com/auth/calendar')
			)
		);

?>