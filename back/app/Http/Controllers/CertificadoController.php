<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use TCPDF;

require base_path('vendor/tecnickcom/tcpdf/tcpdf.php');
class CertificadoController extends Controller{
    public function certificado(Request $request,User $user){
//        $user = $request->user();
        error_log("User". json_encode($user));
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('SIB');
        $pdf->SetTitle('Certificado de registro');
//        $pdf->SetSubject('TCPDF Tutorial');
//        $pdf->SetKeywords('TCPDF, PDF, example, test, guide');

// set default header data
//        $img = public_path('logo.jpg');
//        error_log($img);
//        $urlImg = 'http://localhost:8000/logo.jpg';
//        error_log("PDF_HEADER_LOGO_WIDTH: ".PDF_HEADER_LOGO_WIDTH);
        $header = "C. Belzu No. 650 Entre Vázquez y La Paz
Casilla Postal: 572
BOLIVIA";
        $pdf->SetHeaderData('logo.jpg', PDF_HEADER_LOGO_WIDTH, 'SOCIEDAD DE INGENIEROS DE BOLIVIA', $header, array(0,130,0), array(0,0,0));
        $pdf->setFooterData(array(0,64,0), array(0,64,128));

// set header and footer fonts
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
//
//// set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
//
//// set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
//
//// set some language-dependent strings (optional)
//        if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
//            require_once(dirname(__FILE__).'/lang/eng.php');
//            $pdf->setLanguageArray($l);
//        }

// ---------------------------------------------------------

// set default font subsetting mode
        $pdf->setFontSubsetting(true);

// Set font
// dejavusans is a UTF-8 Unicode font, if you only need to
// print standard ASCII chars, you can use core fonts like
// helvetica or times to reduce file size.
        $pdf->SetFont('dejavusans', '', 11, '', true);

// Add a page
// This method has several options, check the source code documentation for more information.
        $pdf->AddPage();

// set text shadow effect
//        $pdf->setTextShadow(array('enabled'=>true, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));

// Set some content to print
        $html = <<<EOD
<h4 style="text-align: center">CERTIFICADO DE REGISTRO PROFESIONAL</h4>
<p><b>Nombre del profesional:</b></p>
<p style="text-align: center;text-transform: capitalize">$user->name</p>
<p><b>Numero profesional:</b> $user->rni</p>
<p><b>Fecha de registro:</b> $user->fechaInscripcion</p>
<p><b>Fecha de vencimiento:</b> $user->fechaValido</p>
<p><b>Dirección:</b> $user->direccion</p>
<p>El presente certificado es válido para la presentación de proyectos y/o obras ante las entidades públicas y privadas.</p>
EOD;
        $style = array(
            'border' => 0,
            'vpadding' => 'auto',
            'hpadding' => 'auto',
            'fgcolor' => array(0,0,0),
            'bgcolor' => false, //array(255,255,255)
            'module_width' => 1, // width of a single module in points
            'module_height' => 1 // height of a single module in points
        );
        $pdf->write2DBarcode($user->name.' '.$user->rni, 'QRCODE,L', 150, 30, 50, 50, $style);


// Print text using writeHTMLCell()
        $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 0, 0, true, '', true);
//        $pdf->Text(20, 25, 'www');
// ---------------------------------------------------------

// Close and output PDF document
// This method has several options, check the source code documentation for more information.
        $pdf->Output('example_001.pdf', 'I');
    }
}
