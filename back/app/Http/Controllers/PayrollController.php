<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Http\Requests\StorePayrollRequest;
use App\Http\Requests\UpdatePayrollRequest;
use Illuminate\Support\Facades\DB;
use TCPDF;

require base_path('vendor/tecnickcom/tcpdf/tcpdf.php');
class PayrollController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StorePayrollRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePayrollRequest $request)
    {
        //
        //return $request;
        $dia=$request->dias;
        $mes=$request->mes['valor'];
        $generar=DB::SELECT("SELECT e.id,e.nombre,e.apellido,e.ci, count(*) as dias,c.salario, (count(*)/$dia) * c.salario as cancelar 
        FROM attendances t inner join employees e on e.id=t.employee_id inner join charges c on c.id=e.charge_id 
        WHERE month(t.fecha)='$mes' and Year(t.fecha)='$request->anio' and HOUR(CONVERT(t.total,time))>0
        group by e.id,e.nombre,e.apellido,e.ci,c.salario");
        foreach ($generar as $value) {
            # code...    'fecha',
            if(Payroll::where('employee_id',$value->id)->where('mes',$mes)->where('anio',$request->anio)->count()==0)
            {$payroll=new Payroll();
            $payroll->mes=$mes;
            $payroll->anio=$request->anio;
            $payroll->fecha=date('Y-m-d');
            $payroll->salario=$value->salario;
            $payroll->bonificacion=0;
            $payroll->descuento=number_format(($value->salario - $value->cancelar), 2, '.', ''); 
            $payroll->days=$value->dias;
            $payroll->total=number_format($value->cancelar, 2, '.', ''); 
            $payroll->employee_id=$value->id;
            $payroll->save();  }
        }

        //$this->generarPdf($mes,$request->anio);

    }

    public function generarPdf($mes,$anio){
        //        $user = $request->user();
        //error_log("User". json_encode($user));
        $datos= Payroll::with('employee')->where('mes',$mes)->where('anio',$anio)->get();

        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('SIB ORURO');
        $pdf->SetTitle('PLANILLA DE CANCELACION');
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
        $pdf->SetHeaderData('logo.jpg', PDF_HEADER_LOGO_WIDTH, 'SOCIEDAD DE INGENIEROS DE BOLIVIA REGIONAL ORURO', $header, array(0,130,0), array(0,0,0));
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
    $cadena='';
foreach ($datos as $value) {
    # code...
    $cadena="<tr><td>".$value['employee']['ci']."</td><td>".$value['employee']['nombre']."</td><td>".$value['employee']['apellido']."</td><td>".$value['salario']."</td><td>".$value['days']."</td><td>".$value['total']."</td></tr>";
}
        $html = <<<EOD
<h4 style="text-align: center">PLANILLA DE CANCELACION $mes / $anio</h4>
<table>
<thead>
<tr><th>CI</th><th>NOMBRE</th><th>APELLIDO</th><th>SALARIO</th><th>DIAS TRABAJADOS</th><th>CANCELACION</th></tr>
</thead>
<tbody>
$cadena
</tbody>
</table>
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
        //$pdf->write2DBarcode($user->name.' '.$user->rni, 'QRCODE,L', 150, 30, 50, 50, $style);


// Print text using writeHTMLCell()
        $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 0, 0, true, '', true);
//        $pdf->Text(20, 25, 'www');
// ---------------------------------------------------------

// Close and output PDF document
// This method has several options, check the source code documentation for more information.
        $pdf->Output("planilla$mes$anio.pdf", 'D');

    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Payroll  $payroll
     * @return \Illuminate\Http\Response
     */
    public function show(Payroll $payroll)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Payroll  $payroll
     * @return \Illuminate\Http\Response
     */
    public function edit(Payroll $payroll)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdatePayrollRequest  $request
     * @param  \App\Models\Payroll  $payroll
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePayrollRequest $request, Payroll $payroll)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Payroll  $payroll
     * @return \Illuminate\Http\Response
     */
    public function destroy(Payroll $payroll)
    {
        //
    }
}
