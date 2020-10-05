<?php

// set logger configurations
ini_set("display_errors", false);
ini_set("error_log", "logs/logs.log");

/* gdrive class */
class gdrive
{
    //variables
    public $fileRequest;
    public $mimeType;
    public $filename;
    public $path;
    public $gdriveClient;

    /* gdrive constructor */
    public function __construct()
    {
        require_once 'vendor/autoload.php';
        $this->gdriveClient = new Google_Client();
    }

    /* gdrive initializer */
    public function initialize($filePathUploaded)
    {
        $this->fileRequest = $filePathUploaded;

        error_log("initializing class");
        $gdriveClient = $this->gdriveClient;

        $gdriveClient->setAuthConfig('credentials.json');

        $refreshToken = file_get_contents(__DIR__ . "/token.txt");
        $gdriveClient->refreshToken($refreshToken);
        $tokens = $gdriveClient->getAccessToken();
        $gdriveClient->setAccessToken($tokens);

        $gdriveClient->setDefer(true);
        $this->processFile();

    }

    /* file processor function */
    public function processFile()
    {

        $fileRequest = $this->fileRequest;
        error_log("Process File $fileRequest");
        $path_parts = pathinfo($fileRequest);
        $this->filePathUploaded = $path_parts['dirname'];
        $this->fileNameUploaded = $path_parts['basename'];

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $this->mimeType = finfo_file($finfo, $fileRequest);
        finfo_close($finfo);

        error_log("Mime type is " . $this->mimeType);
        $this->upload();

    }

    /* upload function */
    public function upload()
    {
        $gdriveClient = $this->gdriveClient;

        $file = new Google_Service_Drive_DriveFile();
        $file->title = $this->fileNameUploaded;
        $chunkSizeBytes = 1 * 1024 * 1024;

        $fileRequest = $this->fileRequest;
        $mimeType = $this->mimeType;

        $service = new Google_Service_Drive($gdriveClient);
        $request = $service
            ->files
            ->create($file);

        // Create a media file upload to represent our upload process.
        $media = new Google_Http_MediaFileUpload($gdriveClient, $request, $mimeType, null, true, $chunkSizeBytes);
        $media->setFileSize(filesize($fileRequest));

        // Upload the various chunks. $status will be false until the process is
        // complete.
        $status = false;
        $handle = fopen($fileRequest, "rb");

        // start uploading
        error_log("Uploading: " . $this->fileNameUploaded );

        try{
            $filesize = filesize($fileRequest);

            // while not reached the end of file marker keep looping and uploading chunks
            while (!$status && !feof($handle)){
                $chunk = fread($handle, $chunkSizeBytes);
                $status = $media->nextChunk($chunk);
            }

            // The final value of $status will be the data from the API for the object
            // that has been uploaded.
            $result = false;
            if ($status !== false)
            {
                $result = $status;
            }

            fclose($handle);
            // Reset to the gdriveClient to execute requests immediately in the future.
            $gdriveClient->setDefer(false);

            // success uploading
            error_log("Success uploading: " . $this->fileNameUploaded );

        }catch(Exception $e){ // exception uploading
            error_log("exception uploading: " . (string)$e);
        }
    }


    /* get uploaded files */
    public function getFiles(){
        $gdriveClient = $this->gdriveClient;
        $service = new Google_Service_Drive($gdriveClient);

        // Print the names and IDs for up to 10 files.
        $optParams = array(
            'pageSize' => 10,
            'fields' => 'nextPageToken, files(id, name)'
        );
        $results = $service
            ->files
            ->listFiles($optParams);

        if (count($results->getFiles()) === 0){
            echo '<div class="alert alert-warning" role="alert">
                  No files found!
                </div>';
        }
        else{
            foreach ($results->getFiles() as $file)
            {
                echo '<div class="alert alert-success" role="alert">
                      File: ' . $file->getName() . ' ID: ' . $file->getId() . '
                    </div>';
            }
        }
    }

}

?>
