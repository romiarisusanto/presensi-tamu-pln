<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class LaporanExport implements FromCollection, WithHeadings, WithMapping, WithEvents
{
    protected $laporans;
    protected $periode;

    public function __construct($laporans, $periode)
    {
        $this->laporans = $laporans;
        $this->periode = $periode;
    }
    
    public function collection()
    {
        return $this->laporans;
    }

    public function headings(): array
    {
        return [
            ['Rekap Kunjungan PLN'],             
            ["Periode: {$this->periode}"],       
            [],                                  
            [
                'No',
                'Nama',
                'Alamat/Instansi',
                'Jumlah',
                'Waktu Masuk',
                'Waktu Keluar',
                'Keperluan',
                'Bukti Identitas',
                'No Kartu Zona',
                'Jenis Kendaraan',
                'No Kendaraan',
                'Tujuan Unit/PIC'
            ]
        ];
    }

    public function map($laporan): array
    {
        static $no = 0; // auto increment nomor urut
        $no++;

        return [
            $no,
            $laporan->name,
            $laporan->alamat,
            $laporan->jumlah,
            $laporan->created_at ? $laporan->created_at->format('d-m-Y H:i') : null,
            $laporan->keluar,
            $laporan->keperluan,
            $laporan->identitas,
            $laporan->daerah,
            $laporan->nokartu,
            $laporan->nopol,
            $laporan->tujuan_id,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Merge cell untuk judul (12 kolom = A sampai L)
                $event->sheet->mergeCells('A1:L1');
                $event->sheet->mergeCells('A2:L2');

                // Styling judul
                $event->sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 16],
                    'alignment' => ['horizontal' => 'center'],
                ]);
                $event->sheet->getStyle('A2')->applyFromArray([
                    'font' => ['italic' => true, 'size' => 12],
                    'alignment' => ['horizontal' => 'center'],
                ]);
            }
        ];
    }
}
