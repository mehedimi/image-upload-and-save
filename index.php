<?php
	
	$db = new PDO('mysql:host=127.0.0.1;dbname=img_upload', 'root', '');

	spl_autoload_register(function($name){
		require_once "classes/{$name}.php";
	});
	
	$image = new ImageUpload($db);
	
	$image->setPath('uploads');


	

?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>Image Upload</title>
	</head>
	<body>
	<style>
		.container{
			width: 300px;
			margin: 200px auto;
		}
	</style>
		<div class="container">
		
			<?php


				if (!empty($_FILES)) {
					
					$image->fileValidate('image', [
						'required' => true,
						'fileType' => "jpeg,jpg,png",
						'fileSize' => 2
					]);

					if ($image->hasError()) {
						echo '<ul><li>' . implode('</li><li>', $image->errors()) . '</li></ul>';
					}else{
						if($file_name = $image->upload()){

							$query = $db->prepare("INSERT INTO images (name) VALUES (?)");

							$query->execute([$file_name]); 

						}else{
							echo "File upload fails. Try Again";
						}
					}
					
				}

				$images = $db->query("SELECT * FROM images")->fetchALL(PDO::FETCH_OBJ);
			?>
		
		<?php foreach($images as $im) :?>
			<img style="max-width: 100%" src="<?= $im->name;?>" alt=""> <br>
		<?php endforeach;?>
			
			<form action="" method="post" enctype="multipart/form-data">
				<div>
					<input type="file" name="image" >
				</div>
				<hr>
				<div>
					<button type="submit">Upload</button>
				</div>
			</form>
		</div>
	</body>
</html>