<?php
/************************************************************* 
 * This script is developed by Arturs Sosins aka ar2rsawseen, http://webcodingeasy.com 
 * Fee free to distribute and modify code, but keep reference to its creator 
 *
 * This class can generate CSS sprite image from multiple provided images
 * Generated image can be outputted to browser, saved to specified location, etc.
 * This class also generates CSS code for sprite image, using element ID's provided with image.
 * It facilitates CSS sprite implementations to existing website structures
 * 
 * For more information, examples and online documentation visit:  
 * http://webcodingeasy.com/PHP-classes/CSS-sprite-class-for-creating-sprite-image-and-CSS-code-generation
**************************************************************/

exit("comment this line"); // comment this line when testing


$images_source_folder_regex = './test_images/*';
include("./css_sprite.class.php");




//declaring class instance
$sprite = new spritify();

//adding test images 
$files_array=glob($images_source_folder_regex);
foreach ($files_array as $file)
	$sprite->add_image($file);

//retrieving error
$arr = $sprite->get_errors();
//if there are any then output them
if(!empty($arr))
{
	foreach($arr as $error) echo "<p>".$error."</p>"; 
}
else
{
	// ### EXAMPLE 1 ###
	// To Just output the image on-the-fly into browser (for download)
	// $sprite->display_image();
	
	
	// ### EXAMPLE 2 ###
	$file_img = "./image_output_example.png";
	$sprite->save_image($file_img);
	
	//else generate CSS code for added images
	$string = $sprite->generate_css($file_img);
	
	//outputting HTML
	echo '<style type="text/css">'.$string. '</style>';
	foreach ($files_array as $each){
		echo '<div id="'.$sprite->default_filetag($each).'"></div>';
	}


	// ### EXAMPLE3 ###
	// returns GD image resource
	// $sprite->get_resource();
}
?>