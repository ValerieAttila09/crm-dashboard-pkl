<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Deal;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;

class ExportController extends Controller
{
    /**
     * Export Data Customers
     */
    public function exportCustomers(Request $request)
    {
        $format = strtolower($request->get('format', 'csv'));
        $customers = Customer::select('name', 'email', 'phone', 'company', 'status', 'created_at')->get();

        $filename = 'customers_export_' . date('Y-m-d_H-i-s');

        if ($format === 'json') {
            return response()->json($customers, 200, [
                'Content-Disposition' => "attachment; filename=\"{$filename}.json\"",
            ]);
        }

        // CSV & Excel compatible format
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}.csv\"",
        ];

        $callback = function () use ($customers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Nama', 'Email', 'Telepon', 'Perusahaan', 'Status', 'Tanggal Dibuat']);

            foreach ($customers as $c) {
                fputcsv($file, [
                    $c->name,
                    $c->email,
                    $c->phone ?? '-',
                    $c->company ?? '-',
                    strtoupper($c->status),
                    $c->created_at ? $c->created_at->format('Y-m-d H:i') : '-',
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export Data Deals
     */
    public function exportDeals(Request $request)
    {
        $format = strtolower($request->get('format', 'csv'));
        $deals = Deal::with('customer')->get();

        $filename = 'deals_export_' . date('Y-m-d_H-i-s');

        if ($format === 'json') {
            $data = $deals->map(function ($d) {
                return [
                    'id' => $d->id,
                    'title' => $d->title,
                    'amount' => (float) $d->amount,
                    'stage' => $d->stage,
                    'customer' => $d->customer ? $d->customer->name : null,
                    'created_at' => $d->created_at,
                ];
            });

            return response()->json($data, 200, [
                'Content-Disposition' => "attachment; filename=\"{$filename}.json\"",
            ]);
        }

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}.csv\"",
        ];

        $callback = function () use ($deals) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Judul Deal', 'Pelanggan', 'Nominal (Rp)', 'Stage Pipeline', 'Tanggal Dibuat']);

            foreach ($deals as $d) {
                fputcsv($file, [
                    $d->title,
                    $d->customer ? $d->customer->name : '-',
                    $d->amount,
                    strtoupper($d->stage),
                    $d->created_at ? $d->created_at->format('Y-m-d H:i') : '-',
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export Data Tasks
     */
    public function exportTasks(Request $request)
    {
        $format = strtolower($request->get('format', 'csv'));
        $tasks = Task::with('deal')->get();

        $filename = 'tasks_export_' . date('Y-m-d_H-i-s');

        if ($format === 'json') {
            $data = $tasks->map(function ($t) {
                return [
                    'id' => $t->id,
                    'title' => $t->title,
                    'due_date' => $t->due_date ? $t->due_date->format('Y-m-d') : null,
                    'is_completed' => $t->is_completed,
                    'deal' => $t->deal ? $t->deal->title : null,
                ];
            });

            return response()->json($data, 200, [
                'Content-Disposition' => "attachment; filename=\"{$filename}.json\"",
            ]);
        }

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}.csv\"",
        ];

        $callback = function () use ($tasks) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Judul Tugas', 'Tenggat Waktu', 'Status', 'Deal Terkait']);

            foreach ($tasks as $t) {
                fputcsv($file, [
                    $t->title,
                    $t->due_date ? $t->due_date->format('Y-m-d') : '-',
                    $t->is_completed ? 'SELESAI' : 'PENDING',
                    $t->deal ? $t->deal->title : '-',
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}