@extends('layouts.template')

@section('content')
<div class="container mt-3">
    <!-- Filter Section -->
    <div class="row justify-content-between my-3">
        <!-- Bagian Kiri -->
        <div class="col-md-6">
            <form action="{{ route('kasir.order.filter') }}" method="post"> {{-- untuk search di urlnya --}}
                @csrf
                <div class="input-group">
                    <input type="date" name="filter" id="filter" class="form-control m-2">
                    <div class="input-group-append">
                        <button class="btn btn-info m-2" id="cari_data">Cari Data</button>
                        <button class="btn btn-secondary" id="clear_data">Clear</button>
                    </div>
                </div>
            </form>
        </div>
        
        <!-- Bagian Kanan -->
        <div class="col-md-6 text-md-right">
            <a href="{{ route('kasir.order.create') }}" class="btn btn-primary ml-auto m-2">Pembelian Baru</a>
        </div>
    </div>

    <!-- Order Table -->
    <table class="table table-striped table-bordered table-hover">
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th>Pembeli</th>
                <th>Obat</th>
                <th>Total Bayar</th>
                <th>Kasir</th>
                <th>Tanggal Beli</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($orders as $item ) 
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->name_customer }}</td>
                    <td>
                        @foreach ($item->medicines as $index => $medicine )
                            <ol class="medicineList-{{ $loop->parent->iteration }}-{{ $index + 1 }}">
                                <li>
                                    {{ $medicine['name_medicine'] }} ({{ number_format($medicine['price'], 0, '.','.') }}) : Rp. {{ number_format($medicine['sub_price'], 0, '.','.') }}<small> qty {{ $medicine['qty'] }}</small>
                                </li>
                            </ol>
                        @endforeach
                    </td>
                    <td>{{ number_format($item->total_price,0,',','.') }}</td>
                    <td>{{ $item->user->nama }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->created_at)->translatedFormat('j F Y') }}</td>   
                    <td><a href="{{ route('kasir.order.download', $item->id) }}" class="btn btn-secondary">Download Struk</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Pagination Section -->
    <div class="d-flex justify-content-end">
        @if ($orders->count())
            {{ $orders->links() }}
        @endif
    </div>
</div>
@endsection

<!-- Script for Numbering List Items -->
{{-- <script>
    document.addEventListener('DOMContentLoaded', function () {
        // Loop through each medicine list
        @foreach ($orders as $item)
            @foreach ($item->medicines as $index => $medicine)
                var olList{{ $loop->parent->iteration }}{{ $index + 1 }} = document.querySelector('.medicineList-{{ $loop->parent->iteration }}-{{ $index + 1 }}');
                var items{{ $loop->parent->iteration }}{{ $index + 1 }} = olList{{ $loop->parent->iteration }}{{ $index + 1 }}.getElementsByTagName('li');

                for (var i = 0; i < items{{ $loop->parent->iteration }}{{ $index + 1 }}.length; i++) {
                    // Add numbering to each item in the list
                    items{{ $loop->parent->iteration }}{{ $index + 1 }}[i].innerHTML = (i + 1) + '. ' + items{{ $loop->parent->iteration }}{{ $index + 1 }}[i].innerHTML;
                }
            @endforeach
        @endforeach
    });
</script> --}}
