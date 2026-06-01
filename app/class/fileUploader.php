<?php
class ImageUploader {
    private $pdo = null;
    private $allowed = ['image/jpeg', 'image/png']; // look for MIME-types
    private $path = '/path/to/main-images'; // destination path
    
    public function __construct($pdo){ // hold database connection
        $this->pdo = $pdo;
    }
    
    public function upload($file,$upload_dir){
        $maxsize    = 209715200;
        $filename_new = "";
        $acceptable = array(
        'image/jpeg',
        'image/jpg',
        'image/gif',
        'image/png',
        'application/pdf');      
        if(is_uploaded_file($file["tmp_name"]))
        {   
            // newly added for image validation
            //   error_reporting(0);
            //   $file_info = getimagesize($file['tmp_name']);
            //   if(empty($file_info)) // No Image?
            //   {
            //     $erroMessage .= "<li> Invalid <b>image</b> file type. Only JPEG,JPG,GIF and PNG types are accepted.</li>";
            //     $errorFlag = true;
            //   }
            // newly added for image validation        
          if($file['size']>=$maxsize || $file['size'] == 0)
          { 
            $erroMessage .= "<li>Image size to large .</li>";
            $errorFlag = true;  
          }
          else if(!in_array($file['type'],$acceptable))
          {
            $erroMessage .= "<li> Invalid file type. Only JPEG,JPG, GIF and PNG types are accepted.!.</li>";  
            $errorFlag = true;
          }       
          if($erroMessage=='')
          { 
            $erroMessage = "Success";
            $path = $file['name'];      
            $ext = pathinfo($path, PATHINFO_EXTENSION);   
            $filename_new= time().'.'.$ext;  
            $upload_file=copy($file['tmp_name'], "./uploads/".$upload_dir."/".$filename_new); 
            $errorFlag = false;
            return $filename_new;
          } 
          else {
            return false;
          }  
          
         
        }
    }
}
class AudioUploader {
  private $pdo = null;
  private $allowed = ['audio/mpeg', 'audio/wav', 'audio/ogg']; // allowed MIME types
  private $path = '/path/to/audio-files'; // destination path
  
  public function __construct($pdo) { // hold database connection
      $this->pdo = $pdo;
  }
  
  public function upload($file, $upload_dir) {
      $maxsize = 5242880; // maximum size in bytes (5MB)
      $filename_new = "";
      $erroMessage = '';
      $errorFlag = false;
      
      if (is_uploaded_file($file["tmp_name"])) {
          // Validate audio file size
          if ($file['size'] >= $maxsize || $file['size'] == 0) { 
              $erroMessage .= "<li>Audio file size too large. Maximum allowed size is 5MB.</li>";
              $errorFlag = true;  
          } 
          // Validate MIME type
          else if (!in_array($file['type'], $this->allowed)) {
              $erroMessage .= "<li>Invalid file type. Only MP3, WAV, and OGG types are accepted.</li>";  
              $errorFlag = true;
          }       
          // If no errors, proceed with upload
          if ($erroMessage == '') { 
              $erroMessage = "Success";
              $path = $file['name'];      
              $ext = pathinfo($path, PATHINFO_EXTENSION);   
              $filename_new = time() . '.' . $ext;  
              $upload_file = copy($file['tmp_name'], "./uploads/" . $upload_dir . "/" . $filename_new); 
              $errorFlag = false;
              return $filename_new; // return new filename
          } else {
              return false; // return false if there's an error
          }  
      }
      return false; // return false if file wasn't uploaded
  }
}
class ImageUploaderMultiple {
  private $pdo = null;
  private $allowed = ['image/jpeg', 'image/png']; // Allowed MIME types
  private $path = '/path/to/main-images'; // Destination path
  
  public function __construct($pdo) { // Hold database connection
      $this->pdo = $pdo;
  }
  
  public function upload($file, $upload_dir) {
      return $this->uploadSingleImage($file, $upload_dir);
  }

  public function uploadMultiple($files, $upload_dir) {
      $uploadedFiles = [];
      $maxsize = 2097152; // 2 MB
      $acceptable = ['image/jpeg', 'image/jpg', 'image/gif', 'image/png'];

      foreach ($files['tmp_name'] as $key => $tmpName) {
          $erroMessage = '';
          $errorFlag = false;

          if (is_uploaded_file($tmpName)) {
              $file = [
                  'tmp_name' => $tmpName,
                  'name' => $files['name'][$key],
                  'size' => $files['size'][$key],
                  'type' => $files['type'][$key]
              ];
              
              // Validate file size
              if ($file['size'] >= $maxsize || $file['size'] == 0) { 
                  $erroMessage .= "<li>Image size too large for file: {$file['name']}.</li>";
                  $errorFlag = true;  
              }
              // Validate file type
              else if (!in_array($file['type'], $acceptable)) {
                  $erroMessage .= "<li>Invalid file type for file: {$file['name']}. Only JPEG, JPG, GIF, and PNG types are accepted!</li>";  
                  $errorFlag = true;
              }       

              if (!$errorFlag) {
                  $ext = pathinfo($file['name'], PATHINFO_EXTENSION);   
                  $filename_new = time() . '_' . uniqid() . '.' . $ext;  
                  $upload_file = copy($file['tmp_name'], "./uploads/" . $upload_dir . "/" . $filename_new); 

                  if ($upload_file) {
                      $uploadedFiles[] = $filename_new; // Add the filename to the array of uploaded files
                  } else {
                      $erroMessage .= "<li>Failed to upload file: {$file['name']}.</li>";
                  }
              } else {
                  // Here you could log or handle the error messages
                  // For now, just echo them
                  echo $erroMessage;
              }
          }
      }

      return !empty($uploadedFiles) ? $uploadedFiles : false; // Return an array of uploaded filenames or false
  }

  private function uploadSingleImage($file, $upload_dir) {
      // Call the upload method with a single file
      return $this->uploadMultiple(['tmp_name' => [$file['tmp_name']], 'name' => [$file['name']], 'size' => [$file['size']], 'type' => [$file['type']]], $upload_dir);
  }
}