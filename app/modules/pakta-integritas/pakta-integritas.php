<?php
/**
*	Aplikasi Kartu Professional
*	Website	: https://pdsionline.org
* 	Author	: Agus Prawoto Hadi
*	Year	: 2022
*/

use OpenSpout\Reader\Common\Creator\ReaderEntityFactory;

$data['title'] = 'Upload Pakta Integritas';
$data['tabel'] = ['user' => ['file_pakta_integritas' => [
        'url' => BASE_URL . 'public/files/Pakta Integritas.pdf', 
        'title' => 'Download File Pakta Integritas', 
        'display' => 'Pakta Integritas.pdf'
    ]]];

$js[] = BASE_URL . 'public/themes/modern/js/uploadexcel.js';
$js[] = ['print' => true, 'script' => 'var tabel = ' . json_encode($data['tabel'])];

helper('format');		

switch ($_GET['action']) 
{
	default: 
		action_notfound();
		
	// INDEX 
	case 'index':
		
		if (isset($_POST['submit'])) 
		{
			$path = $config['pakta_integritas_path'];
            if (!$_FILES['file_pakta_integritas']['name']) {
                $form_errors['file_pakta_integritas'] = 'File excel belum dipilih';
            }
			
			$form_errors = validate_form();
			if ($form_errors) {
				$data['msg']['status'] = 'error';
				$data['msg']['content'] = $form_errors;
			} else {
				$filename = upload_file($path, $_FILES['file_pakta_integritas']);
                if ($filename) {
                    $data_db['pakta_integritas'] = $filename;

                    $query = $db->update('user', $data_db, 'id_user = ' . $_SESSION['user']['id_user']);
					$data['msg']['status'] = 'ok';
					$data['msg']['content'] = 'Pakta Integritas berhasil di upload';
                } else {
                    $data['msg']['status'] = 'error';
                    $data['msg']['content'] = 'Error saat memperoses ijazah';
                }
				
			}
		}

        $sql = 'SELECT * FROM user WHERE id_user = ?';
        $data['result'] = $db->query($sql, $_SESSION['user']['id_user'])->result();
				
		load_view('views/form.php', $data);
}

function validate_form() {
	
	$form_errors = [];
	if ($_FILES['file_pakta_integritas']['name']) 
	{
        $file_type = $_FILES['file_pakta_integritas']['type'];
        $allowed = ['application/pdf', 'image/png'];

        if (!in_array($file_type, $allowed)) {
            $form_errors['file_pakta_integritas'] = 'Tipe file harus ' . join($allowed, null);
        }

        if ($_FILES['file_pakta_integritas']['size'] > 2000 * 1024) {
            $form_errors['file_pakta_integritas'] = 'Ukuran file maksimal 2MB';
        }
	}
	
	return $form_errors;
}