<?php

namespace App\Exports;

use Excel;
use App\Models\Orders;
use App\Exports\OrdersExport;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class OrdersExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection() //BUAT AMBIL DATA
    {
        return Orders::with('user')->get();
    }

    public function headings(): array //BIKIN NAMA DI PALING ATAS EXCEL
    {
        return [
            "Nama Pembeli", "Obat", "Total Bayar", "Kasir", "Tanggal"
        ];
    }

    public function map($item): array //BUAT NGESYLEING DI EXCELNYA
    {
        $dataObat = '';
        foreach ($item->medicines as $value) {
            $format = $value['name_medicine'] . " (qty " . $value['qty'] . ") : Rp. " . number_format($value['sub_price']) . "),";
            $dataObat .= $format;
        }

        return [
            $item->name_customer,
            $dataObat,
            $item->total_price,
            $item->user->nama,
            \Carbon\Carbon::parse($item->created_at)->isoFormat($item->created_at),
];
}
}
// 'total_price' => number_format($item->total_price, 0, ',', '.'),

