<?php

session_start();

include "upload.php";

//credentials (get those from google developer console https://console.developers.google.com/)
$clientId = '373543127548-t3mmvto1ajocvjmt5dd9bdl8n7dicrog.apps.googleusercontent.com';
$clientSecret = 'Pt6V43-q6WFZEGCQIK3heIaR';
$redirectUri = 'https://lankahot.net/gdriveauth/getTocken.php';

require_once 'vendor/autoload.php'; // get from here https://github.com/google/google-api-php-client.git 

$client = new Google_Client();

// Get your credentials from the console
$client->setApplicationName("Get Token");
// $client->setClientId($clientId);
// $client->setClientSecret($clientSecret);
// $client->setRedirectUri($redirectUri);

$client->setAuthConfig('credentials.json');

$gdrive = new gdrive();

if(isset($_FILES["myFile"])){
    try{
    		
        $file_tmp  = $_FILES["myFile"]["tmp_name"];
        $file_type = $_FILES["myFile"]["type"];
        $file_name = basename($_FILES["myFile"]["name"]);

        $gdrive -> initialize($file_name);
         
	}catch(Exception $e){
        // exception uploading				    
	}
}
		
$client->setScopes(array('https://www.googleapis.com/auth/drive.file'));
$client->setAccessType("offline");
$client->setApprovalPrompt('force');


if (isset($_GET['code'])) {
    $client->authenticate($_GET['code']);
    $_SESSION['token'] = $client->getAccessToken();
	$client->getAccessToken(["refreshToken"]);
    $redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
    header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
    return;
}

if (isset($_SESSION['token'])) {
    $client->setAccessToken($_SESSION['token']);
}

if (isset($_REQUEST['logout'])) {
    unset($_SESSION['token']);
    $client->revokeToken();
    header('Location: https://auth.lankahot.net/gdriveauth/');
    return;
}


?>

<!doctype html>
<html>
    <head><meta charset="utf-8">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js" integrity="sha384-LtrjvnR4Twt/qOuYxE721u19sVFLVSA4hf/rRt6PrZTmiPltdZcI7q7PXQBYTKyf" crossorigin="anonymous"></script>
    </head>
    <body>
        <div class="jumbotron">
          <h1 class="display-4"><img src="https://sites-lvwl.s3.amazonaws.com/wp-content/uploads/2017/08/18004639/google-drive.1c26b303224f5ba52a65ceb313b5139f.png" width="400px" class="ml-3" alt="Asiri Hewage"> Access Tokens</h1>
          <p class="lead">Upload files to your google drive via <a href="https://developers.google.com/drive" class="link" target="_blank">gdrive api</a></p>
          
        <p class="lead">Developed by:</p>
        <img src="https://avatars2.githubusercontent.com/u/12073883?s=460&v=4" width="32px" class="ml-3" alt="Asiri Hewage">
        <img src="https://avatars3.githubusercontent.com/u/28913349?s=460&u=22ea02d6997a6959aef29000962a737638ff549f&v=4" width="32px" class="ml-3" alt="Asiri Hewage">
        
          <hr class="my-4">


        <?php
        if ($client->getAccessToken()) {
            $_SESSION['token'] = $client->getAccessToken();
            echo "<b>Access Token</b> = " . $_SESSION['token']['access_token'] . '<br/><br>';
            echo "<b>Refresh Token</b> = " . $_SESSION['token']['refresh_token'] . '<br/>';
           
			$saveToken = file_put_contents("token.txt",$_SESSION['token']['refresh_token']); // Saving the refresh token in a text file. 
			if ($saveToken){
				echo 'Token saved successfully!<br/><br/>';
			}
			 echo "<a class='btn btn-primary btn-lg' role='button' href='?logout'>Logout</a>";
			 
			 
			 echo '
			 <hr class="my-4">
			 <form enctype="multipart/form-data" method="POST" class="form-inline">
              <div class="form-group">
                <label for="exampleFormControlFile1">Select File to Upload</label>
                <input type="file" class="form-control-file" id="myFile" name= "myFile">
              </div>
              <button type="submit" class="btn btn-primary mb-2">Upload</button>
            </form>
            
            <hr class="my-4">';
            
            $gdrive -> getFiles();
            
		} else {
            $authUrl = $client->createAuthUrl();
            print "<a class='btn btn-primary btn-lg' href='$authUrl'  role='button'>Authorize Google Drive</a>";
        }
        ?>
        
        </div>
    </body>
</html>