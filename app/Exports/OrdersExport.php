<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class OrdersExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnFormatting
{
    protected $orders;
    protected $canteenId;

    public function __construct($orders, $canteenId)
    {
        $this->orders = $orders;
        $this->canteenId = $canteenId;
    }

    public function collection()
    {
        return $this->orders;
    }

    public function headings(): array
    {
        return [
            'Order Number',
            'Queue Number',
            'Customer Name',
            'Customer Email',
            'Meal Name',
            'Quantity',
            'Total Amount (RM)',
            'Status',
            'Order Date',
            'Pickup Time',
            'Special Instructions',
            'Preparation Time',
            'Payment Method',
            'Transaction ID'
        ];
    }

    public function map($order): array
    {
        return [
            $order->order_number,
            $order->queue_number ?? 'N/A',
            $order->user->name,
            $order->user->email,
            $order->meal->name,
            $order->quantity,
            $order->total_amount,
            ucfirst($order->status),
            $order->created_at->format('Y-m-d H:i:s'),
            $order->pickup_time->format('Y-m-d H:i:s'),
            $order->special_instructions ?? 'None',
            $order->meal->preparation_time . ' minutes',
            $order->payment ? ucfirst(str_replace('_', ' ', $order->payment->payment_method)) : 'N/A',
            $order->payment->transaction_id ?? 'N/A'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(15); // Order Number
        $sheet->getColumnDimension('B')->setWidth(12); // Queue Number
        $sheet->getColumnDimension('C')->setWidth(20); // Customer Name
        $sheet->getColumnDimension('D')->setWidth(25); // Customer Email
        $sheet->getColumnDimension('E')->setWidth(25); // Meal Name
        $sheet->getColumnDimension('F')->setWidth(10); // Quantity
        $sheet->getColumnDimension('G')->setWidth(15); // Total Amount
        $sheet->getColumnDimension('H')->setWidth(12); // Status
        $sheet->getColumnDimension('I')->setWidth(18); // Order Date
        $sheet->getColumnDimension('J')->setWidth(18); // Pickup Time
        $sheet->getColumnDimension('K')->setWidth(30); // Special Instructions
        $sheet->getColumnDimension('L')->setWidth(15); // Preparation Time
        $sheet->getColumnDimension('M')->setWidth(15); // Payment Method
        $sheet->getColumnDimension('N')->setWidth(20); // Transaction ID

        // Style the header row
        $sheet->getStyle('A1:N1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4F46E5'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Style the data rows
        $lastRow = count($this->orders) + 1;
        $sheet->getStyle("A2:N{$lastRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'DDDDDD'],
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Alternate row colors
        for ($row = 2; $row <= $lastRow; $row++) {
            $fillColor = $row % 2 == 0 ? 'F8FAFC' : 'FFFFFF';
            $sheet->getStyle("A{$row}:N{$row}")->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->setStartColor(['rgb' => $fillColor]);
        }

        // Auto-filter
        $sheet->setAutoFilter("A1:N{$lastRow}");

        return [];
    }

    public function columnFormats(): array
    {
        return [
            'G' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE, // Total Amount
            'I' => NumberFormat::FORMAT_DATE_DATETIME, // Order Date
            'J' => NumberFormat::FORMAT_DATE_DATETIME, // Pickup Time
        ];
    }
}
