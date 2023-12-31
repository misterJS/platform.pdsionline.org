<?php
/**
* 	PHP *	Year		: 2022
	Author	: Agus Prawoto Hadi
	Created	: 2021
	
	$validation = new FormValidation();
	$validation->setRules('role_nama', 'Nama Role', 'required|unique[role_nama]');
	$validation->setRules('role_judul', 'Judul Role', 'required');
*/
class FormValidation 
{
	private $rules;
	private $messages = [];
	
	public function setRules($input_field, $title, $rules, $func = null) 
	{
		$this->rules[$input_field] = array('field' => $input_field
											, 'title' => $title
											, 'rules' => $rules
											, 'func' => $func
									);
	}
	
	public function validate()
	{
		// echo '<pre>'; print_r($this->rules); die;
		foreach ($this->rules as $field => $arr) 
		{
			$field = trim($field);
			$list_rule = explode('|', $arr['rules']);
			$title = $arr['title'];
			// echo '<pre>'; print_r($list_rule); echo '</pre>';
			foreach ($list_rule as $rule) 
			{
				$input_value = @$_POST[$field];
				$search = $field;

				// If Array
				$is_array = false;
				if (strpos($search, '[')) {
					$is_array = true;
					$exp = explode('[', $field);
					$field = $exp[0];
				}
				
				if ($is_array) {
					$exp_close = explode(']', $exp[1]);
					$index = $exp_close[0];
					$input_value = $_POST[$field][$index];
				}
	
				
				if ($rule == 'trim') {
					if (is_array($input_value)) {
						foreach ($input_value as &$val) {
							$val = trim($val);
						}
					} else {
						$input_value = trim($input_value);
					}
				}
				
				if ($rule == 'required') {
					if (!$input_value) {
						$this->messages[$field] = $title . ' is required';
						break;
					}
				}
				
				if (strpos($rule, 'min_length') !== false) {
					preg_match("/min_length\[(\d+)\]/", $rule, $match);
					if ($match) {
						if (strlen($input_value) < $match[1]) {
							$this->messages[$field] = $title . ' min ' . $match[1] . ' character length';
							break;
						}
					}
				}
				
				if ($rule == 'valid_email') {
					if (filter_var($input_value, FILTER_VALIDATE_EMAIL) === false) {
						
						$this->messages[$field] = $title . ' not valid'; 
						break;
					}
				}
				
				/* Check database tabel name inside brackets [] with column name same as input field name
				* $validation->setRules('module_name', 'Module Name', 'unique[module]');
				* Will check the module table with field of module_name
				* $validation->setRules('module_name', 'Module Name', 'unique[module.field_name]');
				* Will check the module table with field of field_name
				*/
				if (strpos($rule, 'unique') !== false) {
					preg_match('/\[(.*?)\]/', $rule, $match);
					$target = $match[1];
					$exp = explode('.', $target);
					$table = $exp[0];
					if (!empty($exp[1])) {
						$field = $exp[1];
					}

					$check = true;
					$field_old = '';
					if (!empty($_POST[$field . '_old'])) {
						$field_old = trim($_POST[$field . '_old']);
						if (strtolower($input_value) == trim(strtolower($_POST[$field . '_old'])) ) {
							$check = false;
						}
					}
					
					if ($check)
					{
						global $db;
						if ($field_old) {
							$sql = 'SELECT ' . $field . ' FROM ' . $table . ' WHERE ' . $field . ' = ? AND ' . $field . ' != ? ' ;
							$result = $db->query($sql, [$input_value, $field_old])->row();
						} else {
							$sql = 'SELECT ' . $field . ' FROM ' . $table . ' WHERE ' . $field . ' = ?' ;
							$result = $db->query($sql, $input_value)->row();
						}
						if ($result) {
							$this->messages[$field][] = $title . ': <strong>'. $input_value .'</strong> sudah ada di database'; 
						}
					}
				}
				
				
				if (strpos($rule, 'matches') !== false) {
					
					preg_match("/matches\[(.*?)\]/", $rule, $match);
					if ($match) {
						if ($input_value != $_POST[$match[1]]) {
							$this->messages[$field] = $title . ' tidak sama dengan ' . $this->rules[$match[1]]['title'];
							break;
						}
					}
				}
				
				if (strpos($rule, 'check_password') !== false) {
					$this->check_password($input_value, $field);
					break;
				}
			}
			
			if (!key_exists($field, $this->messages)) {
				if ($arr['func']) {
					$message = call_user_func_array($arr['func'], [$input_value]);
					if ($message) {
						$this->messages[$field] = $message;
					}
				}
			}
		}
		
		if ($this->messages) 
			return false;
		
		return true;
	}
	
	public function getMessage() {
		return $this->messages;
	}
	
	public function check_password($password, $field) 
	{
		if (strlen($password) < 8) {
			$this->messages[$field] = 'The Password must constains at least 8 character';
			return false;
		}
		
		preg_match_all("/[a-z]/", $password, $match);
		
		if (!$match[0]) {
			$this->messages[$field] = 'The password must contains at least one small letter';
			return false;
		}
		preg_match_all("/[A-Z]/", $password, $match);
		
		if (!$match[0]) {
			$this->messages[$field] = 'The password must contains at least one capital letter';
			return false;
		}
		preg_match_all("/[0-9]/", $password, $match);
		if (!$match[0]) {
			$this->messages[$field] = 'The password must contains at least one digit character';
			return false;
		}
		
		return true;
	}

}