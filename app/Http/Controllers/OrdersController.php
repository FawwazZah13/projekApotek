<?php

namespace App\Http\Controllers;

use PDF;
use Excel;
use App\Models\Orders;
use App\Models\Medicine;
use Illuminate\Http\Request;
use App\Exports\OrdersExport;
use Illuminate\Support\Facades\Auth;

class OrdersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders= Orders::with('user')->simplePaginate(10);
        return view('order.kasir.index', compact('orders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $medicines = Medicine::all();
        return view('order.kasir.create', compact('medicines'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name_customer' => 'required',
            'medicines' => 'required',
        ],[
            'name_customer.required' => 'Nama Pembeli Harus Diisi',
            'medicines.required' => 'Obat Harus Diisi',
        ]);
        
        //dd($request->medicines); //ambil semua value dari input name=medicines

        $arrayDistinct = array_count_values($request->medicines); //ambil value dan hitung ada berapa
        //id 1 =. 2, obat dengan id dipilih 2 klai atau item => jumlah

        //dd($arrayDistinct)

        $arrayAssocMedicines = [];

        foreach ($arrayDistinct as $id => $count) {
            $medicine = Medicine::where('id', $id)->first();
            $subPrice = $medicine->price * $count;
            $arrayItem= [
                'id' => $id,
                'name_medicine' => $medicine->name,
                'qty' => $count,
                'price' => $medicine->price,
                'sub_price' => $subPrice,
            ];
            array_push($arrayAssocMedicines, $arrayItem);
        }

        // dd($arrayAssocMedicines);

        $totalPrice = 0 ;

        foreach ($arrayAssocMedicines as $item) {
            $totalPrice += (int)$item['sub_price'];
        }

        $priceWithPPN = $totalPrice + ($totalPrice * 0.01);

        $proses = Orders::create([
            'user_id' => Auth::user()->id,
            'medicines' => $arrayAssocMedicines,
            'name_customer' => $request->name_customer,
            'total_price' => $priceWithPPN,
        ]);

        if ($proses) {
            $order = Orders::where('user_id', Auth::user()->id)->orderBy('created_at', 'DESC')->first();
            return redirect()->route('kasir.order.print', $order->id);
        }else {
            //jika berhasil akan di arahkan ke halaman form denan pesan pemberitahuan
            return redirect()->back()->with('failed', 'Gagal membuat data pembelian. Silahkan coba kembali dengan data yang sesuai!');
        }
    }

    /**
     * Display the specified resource.
     */
 
    public function downloadPDF($id)
{
    // ambil data yang diperlukan
    $order = Orders::find($id);

    // pastikan data berformat array 
    $orderArray = $order->toArray();
  
    // mengirim inisial variable dari data yang akan digunakan pada layout pdf 
    view()->share('order', $orderArray);
    
    // panggil blade yang akan di-download 
    $pdf = PDF::loadView('order.kasir.download-pdf', $orderArray);
  
    // kembalikan atau hasilkan bentuk pdf dengan nama file tertentu 
    // return $pdf->download('receipt.pdf');
    return $pdf->download('receipt.pdf');
}


    public function show($id)
    {
        $order = Orders::find($id);
        return view('order.kasir.print', compact('order'));    
    }

    public function filter(Request $request)
    {
        // Mendapatkan nilai tanggal dari input form
        $filterDate = $request->input('filter');
        
        // Jika tidak ada tanggal yang dipilih, kembalikan semua data pesanan
        if (!$filterDate) {
            $orders = Orders::paginate(10); // Sesuaikan dengan model dan metode paginate yang sesuai
            return view('order.kasir.index', compact('orders'));
        }
    
        // Jika ada tanggal yang dipilih, filter data pesanan berdasarkan tanggal
        $orders = Orders::whereDate('created_at', $filterDate)->paginate(10);
    
        return view('order.kasir.index', compact('orders'));
    }

    public function data(){
        $orders = Orders::with('user')->simplePaginate(5);
        return view("order.admin.index", compact('orders'));
    }

    public function exportExcel(){
        $fileName = 'data_pembelian.xlsx';
        return Excel::download(new OrdersExport, $fileName);
        
    }
    
    

    
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Orders $orders)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Orders $orders)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Orders $orders)
    {
        //
    }
}
