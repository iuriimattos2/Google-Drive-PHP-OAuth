<?php

class gdrive{
	
	//credentials (get those from google developer console https://console.developers.google.com/)
	var $clientId = '373543127548-t3mmvto1ajocvjmt5dd9bdl8n7dicrog.apps.googleusercontent.com';
	var $clientSecret = 'Pt6V43-q6WFZEGCQIK3heIaR';
	var $redirectUri = 'https://lankahot.net/gdriveauth/getTocken.php';
	
	//variables
	var $fileRequest;
	var $mimeType;
	var $filename;
	var $path;
	var $client;
	
	
	function __construct(){
		require_once 'vendor/autoload.php'; // get from here https://github.com/google/google-api-php-client.git 
		$this->client = new Google_Client();
	}
	
	
	function initialize($filePath){
	    $this->fileRequest = $filePath;
	    
// 		echo "initializing class\n";
		$client = $this->client;
		
// 		$client->setClientId($this->clientId);
// 		$client->setClientSecret($this->clientSecret);
// 		$client->setRedirectUri($this->redirectUri);
		
		$client->setAuthConfig('credentials.json');
		
		$refreshToken = file_get_contents(__DIR__ . "/token.txt"); 
		$client->refreshToken($refreshToken);
		$tokens = $client->getAccessToken();
		$client->setAccessToken($tokens);
		
		$client->setDefer(true);
		$this->processFile();
		
	}
	
	function processFile(){
		
		$fileRequest = $this->fileRequest;
// 		echo "<br> Process File $fileRequest\n";
		$path_parts = pathinfo($fileRequest);
		$this->path = $path_parts['dirname'];
		$this->fileName = $path_parts['basename'];

		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$this->mimeType = finfo_file($finfo, $fileRequest);
		finfo_close($finfo);
		
// 		echo "<br> Mime type is " . $this->mimeType . "\n";
		
		$this->upload();
			
	}
	
	function upload(){
		$client = $this->client;
		
		$file = new Google_Service_Drive_DriveFile();
		$file->title = $this->fileName;
		$chunkSizeBytes = 1 * 1024 * 1024;
		
		$fileRequest = $this->fileRequest;
		$mimeType = $this->mimeType;
		
		$service = new Google_Service_Drive($client);
		$request = $service->files->create($file);

		// Create a media file upload to represent our upload process.
		$media = new Google_Http_MediaFileUpload(
		  $client,
		  $request,
		  $mimeType,
		  null,
		  true,
		  $chunkSizeBytes
		);
		$media->setFileSize(filesize($fileRequest));

		// Upload the various chunks. $status will be false until the process is
		// complete.
		$status = false;
		$handle = fopen($fileRequest, "rb");
		
		// start uploading		
// 		echo "<br> Uploading: " . $this->fileName . "\n";  
		
		try{
		 		$filesize = filesize($fileRequest);
		
    		// while not reached the end of file marker keep looping and uploading chunks
    		while (!$status && !feof($handle)) {
    			$chunk = fread($handle, $chunkSizeBytes);
    			$status = $media->nextChunk($chunk);  
    		}
    		
    		// The final value of $status will be the data from the API for the object
    		// that has been uploaded.
    		$result = false;
    		if($status != false) {
    		  $result = $status;
    		}
    
    		fclose($handle);
    		// Reset to the client to execute requests immediately in the future.
    		$client->setDefer(false);
    		
    		// success uploading		
    // 		echo "<br> Success uploading: \n"; 
    		
  
		}catch(Exception $e){
            // exception uploading		
    // 		echo "<br> Exception: " . $e . "\n"; 		    
		}

	}
	
	function getFiles(){
	    		$client = $this->client;
	    		$service = new Google_Service_Drive($client);
	    		
	        		// Print the names and IDs for up to 10 files.
              $optParams = array(
                'pageSize' => 10,
                'fields' => 'nextPageToken, files(id, name)'
              );
              $results = $service->files->listFiles($optParams);
            
              if (count($results->getFiles()) == 0) {
                echo'<div class="alert alert-warning" role="alert">
                  No files found!
                </div>';
              } else {
                foreach ($results->getFiles() as $file) {
                    echo '<div class="alert alert-success" role="alert">
                      File: '.$file->getName().' ID: '. $file->getId() .'
                    </div>';
                }
              }
	}
}

?>