<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use Illuminate\Support\Collection;

class RekapAbsensiPengurusExport implements FromCollection, WithTitle, WithStyles, WithColumnWidths, WithEvents
{
    protected array $rekapData;
    protected array $dates;
    protected string $tipe;
    protected string $periode;

    // Baris divisi untuk styling nanti
    protected array $divisiRows   = [];
    protected array $dataRows     = [];
    protected int   $totalRow     = 0;

    // Status color map (ARGB)
    const STATUS_COLORS = [
        'H' => ['bg' => 'FFD1FAE5', 'fg' => 'FF065F46'], // green
        'S' => ['bg' => 'FFFFF3CD', 'fg' => 'FF92400E'], // yellow
        'I' => ['bg' => 'FFDBEAFE', 'fg' => 'FF1E40AF'], // blue
        'A' => ['bg' => 'FFFEE2E2', 'fg' => 'FF991B1B'], // red
        'L' => ['bg' => 'FFF3E8FF', 'fg' => 'FF6B21A8'], // purple - libur
    ];

    public function __construct(array $rekapData, array $dates, string $tipe, string $periode)
    {
        $this->rekapData = $rekapData;
        $this->dates     = $dates;
        $this->tipe      = $tipe;
        $this->periode   = $periode;
    }

    public function title(): string
    {
        return 'Rekap Absensi Pengurus';
    }

    public function collection(): Collection
    {
        $rows          = collect();
        $currentDivisi = null;
        $no            = 1;
        $rowNum        = 2; // baris 1 = header

        // Header row (akan di-handle lewat styles, bukan via WithHeadings)
        $header = ['No', 'Nama', 'Divisi', 'Jabatan'];
        foreach ($this->dates as $d) $header[] = $d;
        $header[] = 'Total B';
        $header[] = 'Total W';
        if ($this->tipe !== 'kepkam') $header[] = 'Total Y';
        $rows->push($header);

        foreach ($this->rekapData as $row) {
            if ($this->tipe !== 'all' && $row['tipe'] !== $this->tipe) continue;

            // Baris divisi sebagai separator
            if ($row['divisi'] !== $currentDivisi) {
                $currentDivisi = $row['divisi'];
                $colCount      = count($header);
                $divisiRow     = array_fill(0, $colCount, '');
                $divisiRow[0]  = '📁 Divisi: ' . $currentDivisi;
                $rows->push($divisiRow);
                $this->divisiRows[] = $rowNum++;
            }

            // Baris data pengurus
            $r = [$no++, $row['nama'], $row['divisi'], $row['jabatan']];

            $sumB = $sumW = $sumY = $totalB = $totalW = $totalY = 0;

            foreach ($this->dates as $date) {
                $att     = $row['attendance'][$date] ?? [];
                $bStatus = $att['bandongan'] ?? null;
                $wStatus = $att['wirid']     ?? null;
                $yStatus = ($this->tipe === 'kepkam') ? null : ($att['yasinan'] ?? null);

                $parts = [];
                if ($bStatus) { $parts[] = "B:{$bStatus}"; if ($bStatus === 'H') $sumB++; $totalB++; }
                if ($wStatus) { $parts[] = "W:{$wStatus}"; if ($wStatus === 'H') $sumW++; $totalW++; }
                if ($yStatus) { $parts[] = "Y:{$yStatus}"; if ($yStatus === 'H') $sumY++; $totalY++; }
                $r[] = empty($parts) ? '-' : implode(' | ', $parts);
            }

            $r[] = "{$sumB}/{$totalB}";
            $r[] = "{$sumW}/{$totalW}";
            if ($this->tipe !== 'kepkam') $r[] = "{$sumY}/{$totalY}";

            $rows->push($r);
            $this->dataRows[] = $rowNum++;
        }

        // Baris total hadir
        $totalRowData = ['', 'TOTAL HADIR', '', ''];
        $sumByDate    = [];
        foreach ($this->dates as $date) {
            $bH = $bT = $wH = $wT = $yH = $yT = 0;
            foreach ($this->rekapData as $row) {
                if ($this->tipe !== 'all' && $row['tipe'] !== $this->tipe) continue;
                $att = $row['attendance'][$date] ?? [];
                if (!is_null($att['bandongan'] ?? null)) { $bT++; if (($att['bandongan'] ?? null) === 'H') $bH++; }
                if (!is_null($att['wirid'] ?? null))     { $wT++; if (($att['wirid']     ?? null) === 'H') $wH++; }
                if ($this->tipe !== 'kepkam' && !is_null($att['yasinan'] ?? null)) { $yT++; if (($att['yasinan'] ?? null) === 'H') $yH++; }
            }
            $parts = ["B:{$bH}/{$bT}", "W:{$wH}/{$wT}"];
            if ($this->tipe !== 'kepkam') $parts[] = "Y:{$yH}/{$yT}";
            $totalRowData[] = implode(' | ', $parts);
            $sumByDate[$date] = compact('bH','bT','wH','wT','yH','yT');
        }

        // Total keseluruhan
        $allBH = array_sum(array_column($sumByDate, 'bH'));
        $allBT = array_sum(array_column($sumByDate, 'bT'));
        $allWH = array_sum(array_column($sumByDate, 'wH'));
        $allWT = array_sum(array_column($sumByDate, 'wT'));
        $totalRowData[] = "{$allBH}/{$allBT}";
        $totalRowData[] = "{$allWH}/{$allWT}";
        if ($this->tipe !== 'kepkam') {
            $allYH = array_sum(array_column($sumByDate, 'yH'));
            $allYT = array_sum(array_column($sumByDate, 'yT'));
            $totalRowData[] = "{$allYH}/{$allYT}";
        }

        $rows->push($totalRowData);
        $this->totalRow = $rowNum;

        return $rows;
    }

    public function columnWidths(): array
    {
        $widths = ['A' => 5, 'B' => 28, 'C' => 20, 'D' => 20];
        $col    = ord('E');
        foreach ($this->dates as $date) {
            $widths[chr($col)] = 14;
            $col++;
        }
        // Summary columns
        $widths[chr($col)]     = 10;
        $widths[chr($col + 1)] = 10;
        if ($this->tipe !== 'kepkam') $widths[chr($col + 2)] = 10;
        return $widths;
    }

    public function styles(Worksheet $sheet): array
    {
        $colCount = 4 + count($this->dates) + ($this->tipe !== 'kepkam' ? 3 : 2);
        $lastCol  = $this->columnLetter($colCount);
        $lastRow  = $this->totalRow;

        return [
            // Header
            1 => [
                'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 10],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF4318FF']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            ],
            // Data area
            "A2:{$lastCol}{$lastRow}" => [
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => false],
                'font'      => ['size' => 9],
            ],
            // Nama & info left-align
            "B2:D{$lastRow}" => [
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
            ],
            // Border
            "A1:{$lastCol}{$lastRow}" => [
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFD1D5DB']]],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet    = $event->sheet->getDelegate();
                $colCount = 4 + count($this->dates) + ($this->tipe !== 'kepkam' ? 3 : 2);
                $lastCol  = $this->columnLetter($colCount);

                // Style baris divisi
                foreach ($this->divisiRows as $r) {
                    $sheet->mergeCells("A{$r}:{$lastCol}{$r}");
                    $sheet->getStyle("A{$r}:{$lastCol}{$r}")->applyFromArray([
                        'font'      => ['bold' => true, 'color' => ['argb' => 'FF1B2559'], 'size' => 9],
                        'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFF8F9FD']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
                    ]);
                }

                // Warnai sel status di kolom tanggal
                foreach ($this->dataRows as $r) {
                    $colIdx = 5; // E = kolom ke-5
                    foreach ($this->dates as $date) {
                        $colLetter = $this->columnLetter($colIdx);
                        $cellValue = $sheet->getCell("{$colLetter}{$r}")->getValue();
                        if ($cellValue && $cellValue !== '-') {
                            // Tentukan warna dominan dari status
                            $dominantStatus = $this->getDominantStatus($cellValue);
                            if ($dominantStatus && isset(self::STATUS_COLORS[$dominantStatus])) {
                                $colors = self::STATUS_COLORS[$dominantStatus];
                                $sheet->getStyle("{$colLetter}{$r}")->applyFromArray([
                                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $colors['bg']]],
                                    'font' => ['color' => ['argb' => $colors['fg']], 'bold' => true, 'size' => 8],
                                ]);
                            }
                        }
                        $colIdx++;
                    }

                    // Warna kolom summary (Total B, W, Y)
                    $sumStartCol = 5 + count($this->dates);
                    $numSumCols  = $this->tipe !== 'kepkam' ? 3 : 2;
                    for ($i = 0; $i < $numSumCols; $i++) {
                        $colLetter = $this->columnLetter($sumStartCol + $i);
                        $sheet->getStyle("{$colLetter}{$r}")->applyFromArray([
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFF4F7FE']],
                            'font' => ['bold' => true, 'color' => ['argb' => 'FF1B2559']],
                        ]);
                    }
                }

                // Style baris total hadir
                if ($this->totalRow > 0) {
                    $r = $this->totalRow;
                    $sheet->getStyle("A{$r}:{$lastCol}{$r}")->applyFromArray([
                        'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 9],
                        'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1B2559']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ]);
                    $sheet->getStyle("B{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                }

                // Freeze header row
                $sheet->freezePane('A2');

                // Row height untuk header
                $sheet->getRowDimension(1)->setRowHeight(25);
            },
        ];
    }

    private function getDominantStatus(string $cellValue): ?string
    {
        // Prioritas: A > S > I > H
        $priority = ['A', 'S', 'I', 'H'];
        foreach ($priority as $status) {
            if (strpos($cellValue, $status) !== false) return $status;
        }
        return null;
    }

    private function columnLetter(int $index): string
    {
        $letter = '';
        while ($index > 0) {
            $mod    = ($index - 1) % 26;
            $letter = chr(65 + $mod) . $letter;
            $index  = (int)(($index - $mod) / 26);
        }
        return $letter;
    }
}
