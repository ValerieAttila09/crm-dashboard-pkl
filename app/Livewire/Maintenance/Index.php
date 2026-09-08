<?php

namespace App\Livewire\Maintenance;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\MaintenanceRequest;
use App\Models\Room;
use App\Models\Customer;
use App\Models\Property;
use App\Models\Lease;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class Index extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $statusFilter = '';
    public $priorityFilter = '';

    // Form Modal
    public $isModalOpen = false;
    public $requestId = null;
    public $room_id, $customer_id, $title, $description, $priority = 'medium', $status = 'pending', $cost = 0;

    protected $rules = [
        'room_id' => 'required|exists:rooms,id',
        'customer_id' => 'nullable|exists:customers,id',
        'title' => 'required|string|max:255',
        'description' => 'required|string',
        'priority' => 'required|in:low,medium,high,urgent',
        'status' => 'required|in:pending,in_progress,completed,cancelled',
        'cost' => 'required|numeric|min:0',
    ];

    public $old_issue_image_url;      // <-- TAMBAHKAN BARIS INI
    public $old_completion_image_url;
    public $issue_image;
    public $completion_image;

    public function store()
    {
        $this->validate([
            'room_id' => 'required|exists:rooms,id',
            'customer_id' => 'nullable|exists:customers,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'cost' => 'required|numeric|min:0',
            'issue_image' => 'nullable|image|max:10240',
            'completion_image' => 'nullable|image|max:10240',
        ]);

        $currentTeam = Auth::user()->currentTeam;
        $bucket = env('AWS_BUCKET', 'room-360');

        // 1. Upload Bukti Kerusakan
        $issueImageUrl = null;
        if ($this->issue_image) {
            $fileName = 'issue_' . $this->issue_image->hashName();
            Storage::disk('supabase')->put($fileName, file_get_contents($this->issue_image->getRealPath()));
            $issueImageUrl = "https://rervvjhlozoxojtikygk.supabase.co/storage/v1/object/public/{$bucket}/{$fileName}";
        }

        // 2. Upload Bukti Selesai Perbaikan
        $completionImageUrl = null;
        if ($this->completion_image) {
            $fileName = 'completion_' . $this->completion_image->hashName();
            Storage::disk('supabase')->put($fileName, file_get_contents($this->completion_image->getRealPath()));
            $completionImageUrl = "https://rervvjhlozoxojtikygk.supabase.co/storage/v1/object/public/{$bucket}/{$fileName}";
        }

        MaintenanceRequest::updateOrCreate(
            ['id' => $this->requestId],
            [
                'team_id' => $currentTeam->id,
                'room_id' => $this->room_id,
                'customer_id' => $this->customer_id ?: null,
                'title' => $this->title,
                'description' => $this->description,
                'priority' => $this->priority,
                'status' => $this->status,
                'cost' => $this->cost,
                'issue_image_url' => $issueImageUrl ?? $this->old_issue_image_url,
                'completion_image_url' => $completionImageUrl ?? $this->old_completion_image_url,
            ]
        );

        session()->flash('message', 'Laporan kerusakan berhasil disimpan.');
        $this->closeModal();
    }

    public function updatedStatus($value)
    {
        // Jika prioritas URGENT / HIGH dan status IN_PROGRESS, otomatis kunci kamar jadi maintenance
        if (in_array($this->priority, ['high', 'urgent']) && $value === 'in_progress' && $this->room_id) {
            Room::where('id', $this->room_id)->update(['status' => 'maintenance']);
        }
    }

    public function updateQuickStatus($id, $newStatus)
    {
        $currentTeam = Auth::user()->currentTeam;
        $request = MaintenanceRequest::where('team_id', $currentTeam->id)->findOrFail($id);

        $request->update(['status' => $newStatus]);

        // Jika status selesai (completed) atau batal (cancelled), kembalikan status kamar jika tadinya maintenance
        if (in_array($newStatus, ['completed', 'cancelled'])) {
            $hasActiveLease = Lease::where('room_id', $request->room_id)->where('status', 'active')->exists();
            $newRoomStatus = $hasActiveLease ? 'occupied' : 'available';
            
            Room::where('id', $request->room_id)->update(['status' => $newRoomStatus]);
        }

        session()->flash('message', "Status perbaikan '{$request->title}' diperbarui menjadi " . strtoupper($newStatus));
    }

    public function getWaVendorUrl($requestId, $vendorPhone = '')
    {
        $currentTeam = Auth::user()->currentTeam;
        $req = MaintenanceRequest::where('team_id', $currentTeam->id)
            ->with(['room.property', 'customer'])
            ->findOrFail($requestId);

        $phone = preg_replace('/[^0-9]/', '', $vendorPhone);
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        $formattedCost = 'Rp ' . number_format($req->cost, 0, ',', '.');
        $tenantInfo = $req->customer ? "{$req->customer->name} ({$req->customer->phone})" : 'Pengelola';

        $message = "🔧 *INSTRUKSI KERJA PERBAIKAN*\n\n"
                 . "📍 *Properti*: {$req->room->property->name}\n"
                 . "🏠 *Unit Kamar*: Kamar {$req->room->room_number}\n"
                 . "⚠️ *Prioritas*: " . strtoupper($req->priority) . "\n"
                 . "📝 *Judul*: {$req->title}\n"
                 . "💬 *Keluhan*: {$req->description}\n"
                 . "👤 *Pelapor*: {$tenantInfo}\n"
                 . "💰 *Estimasi Biaya*: {$formattedCost}\n\n"
                 . "Mohon untuk segera ditindaklanjuti. Terima kasih!";

        return "https://wa.me/{$phone}?text=" . urlencode($message);
    }

    public function render()
    {
        $currentTeam = Auth::user()->currentTeam;

        // 1. Query Laporan Maintenance
        $requests = MaintenanceRequest::where('team_id', $currentTeam->id)
            ->with(['room.property', 'customer'])
            ->when($this->search, function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhereHas('room', fn($r) => $r->where('room_number', 'like', '%' . $this->search . '%'));
            })
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->when($this->priorityFilter, fn($q) => $q->where('priority', $this->priorityFilter))
            ->latest()
            ->paginate(10);

        // 2. LOGIKAH KALKULASI NET PROFIT PROPERTI
        // Total Pendapatan Sewa Lunas (Gross Revenue)
        $grossRevenue = Lease::where('team_id', $currentTeam->id)
            ->where('payment_status', 'paid')
            ->sum('monthly_rent');

        // Total Biaya Perbaikan (Expenses)
        $totalMaintenanceCost = MaintenanceRequest::where('team_id', $currentTeam->id)
            ->where('status', 'completed')
            ->sum('cost');

        // Net Profit Bersih
        $netProfit = $grossRevenue - $totalMaintenanceCost;

        // Data statistik tambahan
        $pendingCount = MaintenanceRequest::where('team_id', $currentTeam->id)->where('status', 'pending')->count();
        $inProgressCount = MaintenanceRequest::where('team_id', $currentTeam->id)->where('status', 'in_progress')->count();

        $rooms = Room::where('team_id', $currentTeam->id)->get();
        $tenants = Customer::where('team_id', $currentTeam->id)->get();

        return view('livewire.maintenance.index', [
            'requests' => $requests,
            'rooms' => $rooms,
            'tenants' => $tenants,
            'grossRevenue' => $grossRevenue,
            'totalMaintenanceCost' => $totalMaintenanceCost,
            'netProfit' => $netProfit,
            'pendingCount' => $pendingCount,
            'inProgressCount' => $inProgressCount,
        ])->layout('layouts.app');
    }

    private function resetInputFields()
    {
        $this->requestId = null;
        $this->room_id = '';
        $this->customer_id = '';
        $this->title = '';
        $this->description = '';
        $this->priority = 'medium';
        $this->status = 'pending';
        $this->cost = 0;
        $this->issue_image = null;
        $this->completion_image = null;
        $this->old_issue_image_url = null;
        $this->old_completion_image_url = null;
        $this->resetValidation();
    }

    public function edit($id)
    {
        $currentTeam = Auth::user()->currentTeam;
        $req = MaintenanceRequest::where('team_id', $currentTeam->id)->findOrFail($id);

        $this->requestId = $id;
        $this->room_id = $req->room_id;
        $this->customer_id = $req->customer_id;
        $this->title = $req->title;
        $this->description = $req->description;
        $this->priority = $req->priority;
        $this->status = $req->status;
        $this->cost = $req->cost;
        $this->old_issue_image_url = $req->issue_image_url;
        $this->old_completion_image_url = $req->completion_image_url;

        $this->isModalOpen = true;
    }
}
