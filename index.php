<style>
	img {
		display: inline;
		margin: 10px;
		width: 150px;
	}
</style>

<?php
/* Instagram App Client Id */
define('INSTAGRAM_CLIENT_ID', 'SEU CLIENT ID');

/* Instagram App Client Secret */
define('INSTAGRAM_CLIENT_SECRET', 'SEU CLIENT SECRET');

/* Instagram App Redirect Url */
define('INSTAGRAM_REDIRECT_URI', 'https://concepts.summercomunicacao.com.br/metro-arts/wp-content/themes/Site/pages/template-instagram-teste.php');

$login_url = 'https://api.instagram.com/oauth/authorize/?client_id=' . INSTAGRAM_CLIENT_ID . '&redirect_uri=' . urlencode(INSTAGRAM_REDIRECT_URI) . '&scope=user_profile,user_media&response_type=code';

$images_links = array();

if ($_GET['code']) {
	$fields = array(
       'client_id'     => '3444083489037969',
       'client_secret' => '039312ea6c578940fd37fa31a00d52c3',
       'grant_type'    => 'authorization_code',
       'redirect_uri'  => 'LINK DE REDIRECT APOS AUTENTICAÇÃO',
       'code'          => $_GET['code']
    );

    $url = 'https://api.instagram.com/oauth/access_token';
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);
    curl_setopt($ch,CURLOPT_POST,true); 
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    $result = curl_exec($ch);
    curl_close($ch); 
    $result = json_decode($result);
    $token = $result->access_token; //your token



	function my_file_get_contents( $site_url ){
		$ch = curl_init();
		$timeout = 5; // set to zero for no timeout
		curl_setopt ($ch, CURLOPT_URL, $site_url);
		curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		ob_start();
		curl_exec($ch);
		curl_close($ch);
		$file_contents = ob_get_contents();
		ob_end_clean();
		return $file_contents;
	}

	$contents = my_file_get_contents('https://graph.instagram.com/me/media?fields=id,username,media_url,media_type,timestamp,children&access_token='.$token);
	$contents = json_decode($contents, true);
    
	$i_f = 0;

	if ($contents['data']) {
	    foreach ($contents['data'] as $post) {

	    	if ($post['children']) {
	    		foreach ($post['children'] as $album) {
	    			$count = count($album);

	    			$i = 0;

	    			while ($i < $count) {
	    				$image_album = my_file_get_contents('https://graph.instagram.com/'.$album[$i]['id'].'?fields=id,media_type,media_url,username,timestamp&access_token='.$token);
	    				$image_album = json_decode($image_album, true);
	    				$link = $image_album['media_url'];

	    					if ($image_album['media_type'] == "IMAGE") {
	    					?>
	    						<img src="<?= str_replace("\/","/", $link)?>">
	    					<?php
	    					}
	    				$i++;
	    			}
	    		}
	    	} else {

	    		if ($post['media_type'] == 'IMAGE') {
	    			$link = $post['media_url'];
		    	?>
		    		<img src="<?= $link; ?>">
		    	<?php
				}
			}

			$images_links[$i_f]['link'] = $link;
			$i_f++;
	    }

	    var_dump($images_links);
	}
}
?>
<body>

<a href="<?= $login_url ?>">Login with Instagram</a>

</body>
</html>
