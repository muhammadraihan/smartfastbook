<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style type="text/css">
        .justify { text-align:justify; }
        .body {
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
        }
        .paging {
            line-height: 25px;
        }

        * {
            box-sizing: border-box;
            -moz-box-sizing: border-box;
        }
        .page {
            width: 210mm;
            min-height: 297mm;
            padding: 20mm;
            margin: 10mm auto;
            border: 1px #D3D3D3 solid;
            border-radius: 5px;
            background: white;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }

        table tr td,
        table tr th{
            font-size: 9pt;
        }
        
        @page {
            size: A4;
            margin: 0;
        }
        @media print {
            html, body {
                width: 210mm;
                height: 297mm;        
            }
            .page {
                margin: 0;
                border: initial;
                border-radius: initial;
                width: initial;
                min-height: initial;
                box-shadow: initial;
                background: initial;
                page-break-after: always;
            }
        }
    </style>
</head>
<div class="book">
    <div class="page">
        <body>
            @php
                $incrementType = 0
            @endphp
            @forelse ($wisma as $type)
                <div class="col-12 col-md-6" data-aos="fade-up-right" data-aos-delay="{{ $incrementType += 100 }}">
                    <div class="category-container">
                        <img src="{{ asset('photo/' . $type->photo) }}" alt="" class="w-100 img-fluid">
                        <div class="desc">
                            <h5 class="text-uppercase">{{ $type->name }}</h5>
                            <a href="{{ route('wisma', $type->uuid) }}" class="link-homestead btn px-4">
                                Lihat Selengkapnya
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12" data-aos="fade-up-right" data-aos-delay="100">
                    No Type Was Found
                </div>
            @endforelse

            <table class='table table-bordered'>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Jenis Laporan</th>
                        <th>Jenis Apresiasi</th>
                        <th>Keterangan</th>
                        <th>Nama Lokasi</th>
                        <th>Alamat</th>
                        <th>Kota</th>
                        <th>Provinsi</th>
                        <th>Negara</th>
                        <th>Place ID</th>
                    </tr>
                </thead>
                <tbody>
                    @php $i=1 @endphp
                    @foreach($cetak as $p)
                    <tr>
                        
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
        </body>
    </div>
</div>