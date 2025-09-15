<?php

namespace App\Exports;

use App\Models\Laporan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LaporanExport implements FromCollection, WithHeadings, WithMapping
{
    protected $period;

    public function __construct($period = 'all')
    {
        $this->period = $period;
    }

    public function collection()
    {
        $query = Laporan::query();

        if ($this->period === 'day') {
            $query->whereDate('created_at', today());
        } elseif ($this->period === 'week') {
            $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($this->period === 'month') {
            $query->whereMonth('created_at', now()->month)
                  ->whereYear('created_at', now()->year);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
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
        ];
    }

    public function map($laporan): array
    {
        return [
            $laporan->id,
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
}
