<?php

class ImageUpload
{
	protected $db;

	protected $errors = [];

	protected $path = null;

	protected $fileName = '';

	protected $messages = [
		'required' => "This File field is required",
		'fileType' => "File type must be :rule_value",
		'fileSize' => "File size must be maximum of :rule_value MB"
	];

	public function __construct(PDO $db)
	{
		$this->db = $db;
	}

	public function setPath($path)
	{
		if (is_dir($path)) {
			
			$this->path = $path;

		}else{
			echo("This File Directory Not Found!");
		}
	}

	public function hasError()
	{
		return !empty($this->errors);
	}

	public function fileValidate($name, $rules)
	{	
		$this->fileName = $name;

		foreach ($rules as $single_rule => $rule_value) {
			
			if (in_array($single_rule, array_keys($this->messages))) {
				
				if (! call_user_func_array([$this, $single_rule], [$rule_value, $_FILES[$name]])) {
					
					$this->errors[] = str_replace([':rule_value'], [$rule_value], $this->messages[$single_rule]);
				}
			}
		}
	}

	public function errors()
	{
		return $this->errors;
	}

	public function upload()
	{	
		$filename = md5(time());
		$fileext = $this->extractFileExtension($_FILES[$this->fileName]['name']);
		if (move_uploaded_file($_FILES[$this->fileName]['tmp_name'], $this->path . DIRECTORY_SEPARATOR . $filename . '.' . $fileext)) {
			return $this->path . DIRECTORY_SEPARATOR .$filename . '.' . $fileext;
		}
		return false;
	}

	protected function extractFileExtension($name)
	{
		$ext = explode('.', $name);

		return strtolower(end($ext));
	}


	protected function required($rules_value, $value)
	{
		return ! empty($value['name']);
	}

	protected function fileType($rules_value, $value)
	{
		return in_array($this->extractFileExtension($value['name']), explode(',', $rules_value));
	}

	protected function fileSize($rules_value, $value)
	{
		return ($rules_value * 1024 * 1024) >=  $value['size'];
	}
}