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
            $form_errors = array();
            if (!$_POST['signature_name']) {
                $form_errors['signature_name'] = 'Masukan Nama yang akan dijadikan signature';
            }
			
			if ($form_errors) {
				$data['msg']['status'] = 'error';
				$data['msg']['content'] = $form_errors;
			} else {
                $filename = sign_in_document($_POST['signature_name'], $config);
                if ($filename) {
                    $data_db['pakta_integritas'] = $filename;

                    $query = $db->update('user', $data_db, 'id_user = ' . $_SESSION['user']['id_user']);
					$data['msg']['status'] = 'ok';
					$data['msg']['content'] = 'Pakta Integritas berhasil di generate';
                } else {
                    $data['msg']['status'] = 'error';
                    $data['msg']['content'] = 'Error saat memperoses Pakta Integritas';
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

function sign_in_document($signature_name, $config){
	require_once('app/libraries/vendors/tcpdf/tcpdf.php');

	$certificate = 'file://'.realpath('tcpdf.pem');
	$certificate_pk = 'file://'.realpath('tcpdf_pk.pem');

	// create new PDF document
	$pdf = new TCPDF();
	$pdf->SetCreator('PDSI');
	$pdf->SetAuthor('PDSI Organization');
	$pdf->SetTitle('Pakta Integritas');
	$pdf->SetSubject('Pakta Integritas');
	$pdf->SetKeywords('');
	// $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 052', PDF_HEADER_STRING);

	// set document information
	$pdf->AddPage('L', 'mm', 'A4');
    $pdf->SetFont('', 'B', 14);
    $pdf->Cell(277, 10, "PAKTA INTEGRITAS", 0, 1, 'C');
	$pdf->Cell(277, 10, "PENGURUS DAN ANGGOTA PERKUMPULAN DOKTER SELURUH INDONESIA", 0, 1, 'C');
    $pdf->SetAutoPageBreak(true, 0);
    // Add Header
    $pdf->Ln(10);
    $pdf->SetFont('', '', 12);
    $pdf->Cell(277, 10, "1. Saya berjanji untuk setia pada Negara Kesatuan Republik Indonesia dan UUD 1945, serta Pancasila sebagai satu-satunya asas", 0, 1, 'L'); 
	$pdf->Cell(277, 10, "dalam bernegara dan berorganisasi", 0, 1, 'L');
    $pdf->Cell(277, 10, "2. Saya berjanji untuk selalu menjaga kehormatan PDSI", 0, 1, 'L');
    $pdf->Cell(277, 10, "3. Saya berjanji selalu mematuhi segala ketentuan dan segala keputusan organisasi, yang sesuai dengan AD/ART dan kode etik dokter", 0, 1, 'L'); 
	$pdf->Cell(277, 10, "seluruh Indonesia", 0, 1, 'L');
    $pdf->Cell(277, 10, "4. Saya berjanji akan melaksanakan dengan sungguh-sungguh semua amanat dan program organisasi", 0, 1, 'L');
    $pdf->Cell(277, 10, "5. Dalam melaksanakan tugas-tugas organisasi, saya mengutamakan sopan-santun, kepentingan bersama, tidak membedakan suku, agama, ras,", 0, 1, 'L'); 
	$pdf->Cell(277, 10, "dan antargolongan, tidak membedakan almamater, menjunjung tinggi asas musyawarah dan mufakat, serta berpedoman pada AD/ART PDSI,", 0, 1, 'L'); 
	$pdf->Cell(277, 10, "sumpah dokter dan kode etik dokter seluruh Indonesia", 0, 1, 'L');
    $pdf->Cell(277, 10, "6. Janji ini saya buat, dalam rangka kedudukan saya sebagai pengurus dan anggota PDSI.", 0, 1, 'L');
    $pdf->Ln(10);
    $pdf->Cell(277, 10, "Semoga Tuhan Yang Maha Esa, selalu menolong saya di dalam menepati janji ini.", 0, 1, 'L');
    $pdf->Ln(10);
    $pdf->SetFont('', '', 12);
    $pdf->SetFont('', 'B', 14);
    $pdf->Cell(277, 10, "Yang berjanji,", 0, 1, 'L');

	$info = array(
		'Name' => 'PDSI Organization',
		'Location' => '',
		'Reason' => 'PDSI Signature',
		'ContactInfo' => 'https://pdsionline.org/',
	);
	$pdf->setSignature($certificate, $certificate_pk, 'pdsionline', '', 2, $info, 'A');
	$pdf->Cell(277, 10, $signature_name, 0, 1, 'L');
    $filename = 'pakta_integritas_' . time() . '.pdf';
	$path_file = BASE_PATH.'public/files/pakta-integritas/'. $filename;
    $pdf->Output($path_file, 'F');
    return $filename;
}
