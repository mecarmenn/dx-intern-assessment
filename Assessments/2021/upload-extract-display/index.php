<?php
    $message = '' ;
    $output = '';
    $current_path = getcwd();
    $upload_dir = "/storage/ssd1/255/16460255/public_html/uploads/";
    if(isset($_POST["btn_zip"])) {
        if($_FILES["zipFile"]["name"]) {
        	$filename = $_FILES["zipFile"]["name"];
        	$source = $_FILES["zipFile"]["tmp_name"];
        	$type = $_FILES["zipFile"]["type"];
        	
        	$name = explode(".", $filename); //split the filename into an array using "." as delimeter, [0] will be filename, [1] will be file extension
        	$accepted_types = array('application/zip', 'application/x-zip-compressed', 'multipart/x-zip', 'application/x-compressed');  //define tyoes of zip that is acceptable by the server during upload
        	foreach($accepted_types as $mime_type) {
        		if($mime_type == $type) {
        			$okay = true;
        			break;
        		} 
        	}
        	
        	$continue = strtolower($name[1]) == 'zip' ? true : false; //check if the extension is zip
        	if(!$continue) {
        		$message = "The file you are trying to upload is not a .zip file. Please try again.";
        	}
        
        	$target_path = $upload_dir.$filename;  // target path is the uploads/<ZipFile> in this case to keep the data in individual extracted folder
        	if(move_uploaded_file($source, $target_path)) { //move the uploaded file inside /tmp to the destination target path
        		$zip = new ZipArchive;
        		if ($zip->open($target_path)) {
        			$zip->extractTo($upload_dir.$name[0]); //open the zip and extract the content to the parent folder
        			$zip->close(); //close the object to prevent leak
        		}
        		$message = "Your .zip file was uploaded and unpacked.";
        		
        		$files = scandir($upload_dir.$name[0]);  //load the files inside the extracted dir
                     #$name[0] is the name of the parent folder after extract from zip file  
                     foreach($files as $file)  //run a loop over every item in it
                     {  
                          $file_ext = explode(".", $file);  //same as line 14, split into 2 section [0] file name, [1] file extension
                          $allowed_ext = array('jpg', 'png');  //only these 2 format is allowed and will be used to display
                          if(in_array($file_ext[1], $allowed_ext))  //if the file extension loaded in for loop matches jpg/png
                          {  
                               //$new_name = md5(rand()).'.'.$file_ext;  temporarily disabled, can remove if dont want to use this approach
                               
                               # append the $output with html code to display in the html section below dynamically
                               # img src finds the images inside uploads/ExtractedDir/<file>.jpg@png
                               $output .= ' <div class="col-md-6">
                                                <div style="padding:16px; border:1px solid #CCC;">
                                                    <img src="uploads/'.$name[0]."/".$file.'" width="170" height="240" /> 
                                                </div>
                                            </div>';
                                            
                               #not sure what 49 and 50 does yet
                               //copy($path.$name.'/'.$file, $path . $new_name);  this seems like make a copy of the spurce image and renamed it with encrypted name (line 49)
                               //unlink($upload_dir.$name[0].'/'.$file);  uncomment if you want to delete the original file (youtube code is keep renamed file)
                          }       
                     }  
                     #line 65 and 66 are delete but i might be wrong about the sequence (vice versa), trial and error required
        	} else {	
        		$message = "There was a problem with the upload. Please try again.";
        	}
        } else {
            $message = "Please choose a zip file to upload";
        }
    }
?>

<!doctype html>
<html lang="en">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0" http-equiv="Content-Type" />
<title>Digi-X Internship Assessment</title>

  <link rel="stylesheet" href="custom.css" />
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.12.1/css/all.css" crossorigin="anonymous">


<section id="head" class="d-flex flex-column justify-content-end align-items-center">
<h1 class="animate__animated animate__fadeInDown">Upload and share your images.</h1>
<h2>Fast and easy zip upload.</h3>
<meta name="description" content="The HTML5 Herald" />
<meta name="author" content="Digi-X Internship Committee" />

</head>
</div>
</section>

<body>
<?php if($message != '') echo "<p>$message</p>"; ?>
<form enctype="multipart/form-data" method="post" action="">
    
<section id="content" class="d-flex flex-column justify-content-end align-items-center">
<label>Choose a zip file to upload: <input type="file" name="zipFile" /></label>
<br />

<input type="submit" name="btn_zip" id="upload" class="btn btn-info" value="Upload" />
</form>

 <!-- DO NO REMOVE CODE STARTING HERE -->
    <div class="display-wrapper" id="notify">

            <!-- THE IMAGES SHOULD BE DISPLAYED INSIDE HERE -->
             <?php  
                if(isset($output)) {  
                     echo $output;  
                }  
            ?> 
        </div>
    </div>
    <!-- DO NO REMOVE CODE UNTIL HERE -->
    
</body>
</html>