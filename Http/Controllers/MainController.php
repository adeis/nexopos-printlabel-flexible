<?php

/**
 * Product Barcode &amp; Generator Controller
 * @since 1.0
 * @package modules/BarcodeGenerator
**/

namespace Modules\BarcodeGenerator\Http\Controllers;

use Mpdf\Mpdf;
use Illuminate\Http\Request;
use App\Services\DateService;
use App\Services\ProductService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\DashboardController;
use App\Models\Product;
use App\Models\ProductUnitQuantity;

//use PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf;

class MainController extends DashboardController
{
    public function __construct(
        protected ProductService $productService,
        protected DateService $dateService
    ) {

    }
    /**
     * Main Page
     * @since 1.0
    **/
    public function index(Request $request)
    {
        if(auth()->guest()) {
            return redirect()->route('ns.login');
        }

        // get path module

        $path = config('BarcodeGenerator.path.resources');
        $jsonPath = $path . '/json';
        $commondFile = $jsonPath . '/common_paper_sizes.json';
        $commondPaperSizes = json_decode(file_get_contents($commondFile), true);
        if($request->get('btn') == 'preview') {
            return $this->setPdf($request);
        }
        if($request->get('btn') == 'print') {
            return $this->setPdf($request);
        }

        $products = $this->products();


        return view('BarcodeGenerator::main.index', [
            'title' => __('Bulk Label Print'),
            'commondPaperSizes' => $commondPaperSizes,
            'products' => $products,

        ]);

    }

    protected function products()
    {
        return $this->productService->getProducts();
    }

    protected function setPdf($request)
    {
        $options = [];
        $size = $request->get('page-size', '190-236');
        $format = explode('-', $size);
        $orientation = $request->get('orientation', 'P');
        if(is_array($request->get('products'))) {
            $productIds = $request->get('products');
        } else {
            $productIds = explode(',', $request->get('products', ''));
        }
        $isCustomSize = $request->get('size_page_custom', "off") == 'on';
        if($isCustomSize) {
            $format = [
                $request->get('custom_page_width', "190"),
                $request->get('custom_page_height', "236"),
            ];
        }
        $total = $request->get('total', 1);
        $options = [
            'format' => $format,
            'orientation' => $orientation,
            'margin_left' => (int) $request->get('page_margin_left', 1),
            'margin_right' => (int) $request->get('page_margin_right', 1),
            'margin_top' => (int) $request->get('page_margin_top', 1),
            'margin_bottom' => (int) $request->get('page_margin_bottom', 1),
        ];
        $barcodeHeight = (float) $request->get('barcode_height', 19);
        $barcodeWidth = (float) $request->get('barcode_width', 33);
        $barcodeMargin = (float) $request->get('barcode_margin', 1);
        $barcodeHeight = $barcodeHeight + $barcodeMargin;
        $barcodeWidth = $barcodeWidth + $barcodeMargin;
        $mpdf = new Mpdf($options);

        //$mpdf->AddPage('L'); // Adds a new page in Landscape orientation
        $listProducts = Product::whereIn('id', $productIds)->get();
        $products = [];
        foreach($listProducts as $dt) {
            $products[$dt->id]["product"] = $dt->toArray();
        }
        $barcodeView = $request->get('barcode_view', []);

        if(in_array('price', $barcodeView)) {
            $unitQuantity = ProductUnitQuantity::whereIn('product_id', $productIds)->get();
            foreach($unitQuantity as $dt) {
                $products[$dt->product_id]["unit_quantity"] = $dt->toArray();
            }
        }

        $columns = $request->get('column', 3);
        $barcodes = [];
        $barcodeTypes = [];
        $barcodeNames = [];
        $barcodePrices = [];
        //dd($products);
        foreach($products as $product) {
            for($i = 0; $i < $total; $i++) {
                $barcodes[] = $product["product"]["barcode"];
                $bTypes = strtoupper($product["product"]["barcode_type"] ?? 'C128B');
                $bTypes = str_replace('CODE', 'C', $bTypes);
                $barcodeTypes[] = $bTypes;
                $name = $product["product"]["name"] ?? "-";
                // cut name if string is too long
                if(strlen($name) > 20) {
                    $name = substr($name, 0, 20)."…";
                }
                $barcodeNames[] = $name;


                if(isset($product["unit_quantity"])) {
                    $price = $product["unit_quantity"]["sale_price"] ?? "-";
                    if($price != "-") {
                        $price = "Rp.".number_format($price, 0, ',', '.');
                    }
                    $barcodePrices[] = $price;
                } else {
                    $barcodePrices[] = "-";
                }

            }
        }
        $totalBarcodes = count($barcodes);

        // Hitung jumlah baris yang dibutuhkan
        $rows = ceil($totalBarcodes / $columns);
        $isBtnPreview = $request->get('btn', "") == 'preview';
        $isBtnPrint = $request->get('btn', "") == 'print';
        $borderColor = "#cccccc";
        if($isBtnPreview) {
            $borderColor = "#cccccc";
        }
        if($isBtnPrint) {
            $borderColor = "#ffffff";
        }
        $html = "
        <style>
        table, th, td {
            border: ".$barcodeMargin."mm solid $borderColor;
            border-collapse: collapse;
          }
        </style>";

        $html .= '<table>';

        // Loop untuk baris

        for ($i = 0; $i < $rows; $i++) {
            $html .= '<tr>';

            // Loop untuk kolom
            for ($j = 0; $j < $columns; $j++) {
                $index = ($i * $columns) + $j;

                // Pastikan tidak melebihi total barcode
                if ($index < $totalBarcodes) {
                    $code = $barcodes[$index];
                    $barcodeType = $this->determineBarcodeType($code);
                    // Tambahkan barcode di sini, sebagai contoh menggunakan teks sebagai placeholder
                    $html .= '<td  style="text-align: center; height: '.$barcodeHeight.'mm; width: '.$barcodeWidth.'mm; padding: '.$barcodeMargin.'mm;"> <div class="box-barcode" style="">';
                    $html .= '<small>';
                    if(in_array('name', $barcodeView)) {
                        $name = $barcodeNames[$index];
                        $html .= "$name";
                    }
                    if(in_array('price', $barcodeView)) {
                        $price = $barcodePrices[$index];
                        $html .= "  $price";
                    }
                    $html .= '</small><br>';
                    $html .= '<barcode code="'.$code.'" type="'.$barcodeType.'" style="" />';
                    $html .= '<br><small>';
                    $html .= "$code";
                    $html .= '</small><br>';
                    $html .= '</div></td>';
                } else {
                    $html .= '<td></td>'; // Jika tidak ada barcode, tambahkan sel kosong
                }
            }

            $html .= '</tr>';
        }

        $html .= '</table>';
        //echo $html; exit;

        $mpdf->WriteHTML($html);

        $namePdf = "barcode".date('YmdHis').".pdf";
        $mpdf->Output($namePdf, 'I');
    }


    public function determineBarcodeType($code)
    {
        if (preg_match('/^\d{13}$/', $code)) {
            return 'EAN13';
        } elseif (preg_match('/^\d{12}$/', $code)) {
            return 'UPCA';
        } elseif (preg_match('/^[A-Z0-9\-\.\ \$\/\+\%]+$/', $code)) {
            return 'C39';
        } else {
            return 'C128B';
        }
    }

}
