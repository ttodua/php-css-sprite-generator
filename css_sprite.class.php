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


/************************************************************* 
 UPDATED REPOSIROTY ON GITHUB:   https://github.com/ttodua/php-css-sprite-generator/
**************************************************************/
class spritify
{
	//image type to save as (for possible future modifications)
	private $image_type = "png";
	//array to contain images and image informations
	private $images = array();
	//array for errors
	private $errors = array();
	
	//gets errors
	public function get_errors(){
		return $this->errors;
	}
	
	/*
	 * adds new image
	 * first parameter - path to image file like ./images/image.png
	 * second parameter (optiona) - ID of element fro css code generation
	 */
	public function add_image($image_path, $id=""){
		if (empty($id)){
			$id = $this->default_filetag($image_path);
		}
		
		if(file_exists($image_path))
		{
			$info = getimagesize($image_path);
			if(is_array($info))
			{
				$new = sizeof($this->images);
				$this->images[$new]["path"] = $image_path;
				$this->images[$new]["width"] = $info[0];
				$this->images[$new]["height"] = $info[1];
				$this->images[$new]["mime"] = $info["mime"];
				$type = explode("/", $info['mime']);
				$this->images[$new]["type"] = $type[1];
				$this->images[$new]["id"] = $id;
			}
			else
			{
				$this->errors[] = "Provided file \"".$image_path."\" isn't correct image format";
			}
		}
		else
		{
			$this->errors[] = "Provided file \"".$image_path."\" doesn't exist";
		}
	}
	
	public function default_filetag($image_path){
		$inf = pathinfo($image_path);
		return "sprite-". $inf['filename'] .'_'. $inf['extension'];
	}
	
	
	//calculates width and height needed for sprite image
	private function total_size(){
		$arr = array("width" => 0, "height" => 0);
		foreach($this->images as $image)
		{
			if($arr["width"] < $image["width"])
			{
				$arr["width"] = $image["width"];
			}
			$arr["height"] += $image["height"];
		}
		return $arr;
	}
	
	//creates sprite image resource
	private function create_image(){
		$total = $this->total_size();
		$sprite = imagecreatetruecolor($total["width"], $total["height"]);
		imagesavealpha($sprite, true);
		$transparent = imagecolorallocatealpha($sprite, 0, 0, 0, 127);
		imagefill($sprite, 0, 0, $transparent);
		$top = 0;
		foreach($this->images as $image)
		{
			$func = "imagecreatefrom".$image['type'];
			$img = $func($image["path"]);
			imagecopy( $sprite, $img, ($total["width"] - $image["width"]), $top, 0, 0,  $image["width"], $image["height"]);
			$top += $image["height"];
		}
		return $sprite;
	}
	
	//outputs image to browser (makes php file behave like image)
	public function output_image(){
		$sprite = $this->create_image();
		header('Content-Type: image/'.$this->image_type);
		$func = "image".$this->image_type;
		$func($sprite); 
		ImageDestroy($sprite);
	}
	
	/*
	 * generates css code using ID provided when adding images or pseido ID "elem"
	 * $path parameter (optional) - takes path to already generated css_sprite file or uses default file for pseudo code generation
	 */
	public function generate_css($path = "/css_sprite.png"){
		$total = $this->total_size();
		$top = $total["height"];
		$css = "";
		foreach($this->images as $image)
		{
			if(strpos($image["id"],"#") === false)
			{
				$css .= "#".$image["id"]." { ";
			}
			else
			{
				$css .= $image["id"]." { ";
			}
			$css .= "background-image: url(".$path."); ";
			$css .= "background-position: ".($image["width"] - $total["width"])."px ".($top - $total["height"])."px; ";
			$css .= "width: ".$image['width']."px; ";
			$css .= "height: ".$image['height']."px; ";
			$css .= "}\n";
			$top -= $image["height"];
		}
		return $css;
	}
	
	// Saves file
	public function save_image($path = ""){
		if ( empty(trim($path)) ) 
			exit("empty path");
		$sprite = $this->create_image();
		$func = "image".$this->image_type;
		$func($sprite, $path); 
		ImageDestroy($sprite);
	}
	
	// Downloads image
	public function display_image(){
		$sprite = $this->create_image();
		$func = "image".$this->image_type;
		header('Content-Description: File Transfer');
		header('Content-Type: image/'.$this->image_type);
		header('Content-Disposition: attachment; filename=css_sprite.'.$this->image_type);
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header('Content-Length: ' . filesize($sprite));
		ob_clean();
		flush();
		$func($sprite); 
		ImageDestroy($sprite);
	}
	
	//return image resource
	public function get_resource(){
		$sprite = $this->create_image();
		return $sprite;
	}
}
?>