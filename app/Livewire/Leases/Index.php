<?php

namespace App\Livewire\Leases;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Lease;
use App\Models\Room;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use App\Notifications\TaskDueNotification;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';

    // Form Modal Fields
    public $isModalOpen = false;
    public $leaseId = null;
    public $room_id, $customer_id, $start_date, $end_date, $monthly_rent = 0, $payment_status = 'unpaid', $status = 'active';

    protected $rules = [
        'room_id' => 'required|exists:rooms,id',
        'customer_id' => 'required|exists:customers,id',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after:start_date',
        'monthly_rent' => 'required|numeric|min:0',
        'payment_status' => 'required|in:paid,unpaid,overdue',
        'status' => 'required|in:active,ended,cancelled',
    ];

    // Tambahkan properti ini di dalam class Index
    public $isCreatingTenant = false;
    public $new_tenant_name = '';
    public $new_tenant_email = '';
    public $new_tenant_phone = '';

    public function toggleCreateTenant()
    {
        $this->isCreatingTenant = !$this->isCreatingTenant;
        $this->new_tenant_name = '';
        $this->new_tenant_email = '';
        $this->new_tenant_phone = '';
    }

    public function storeTenant()
    {
        $this->validate([
            'new_tenant_name' => 'required|string|max:255',
            'new_tenant_email' => 'required|email|max:255',
            'new_tenant_phone' => 'nullable|string|max:50',
        ]);

        $currentTeam = Auth::user()->currentTeam;

        $tenant = Customer::create([
            'team_id' => $currentTeam->id,
            'name' => $this->new_tenant_name,
            'email' => $this->new_tenant_email,
            'phone' => $this->new_tenant_phone,
            'status' => 'customer',
            'created_by' => Auth::id(),
        ]);

        $this->customer_id = $tenant->id; // Otomatis pilih tenant yang baru dibuat
        $this->isCreatingTenant = false;
        $this->new_tenant_name = '';
        $this->new_tenant_email = '';
        $this->new_tenant_phone = '';
    }

    public function create()
    {
        $this->resetInputFields();
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->leaseId = null;
        $this->room_id = '';
        $this->customer_id = '';
        $this->start_date = '';
        $this->end_date = '';
        $this->monthly_rent = 0;
        $this->payment_status = 'unpaid';
        $this->status = 'active';
        $this->resetValidation();
    }

    public function updatedRoomId($value)
    {
        // Otomatis set monthly_rent sesuai harga kamar yang dipilih
        if ($value) {
            $room = Room::find($value);
            if ($room) {
                $this->monthly_rent = $room->price_per_month;
            }
        }
    }

    public function store()
    {
        $this->validate();
        $currentTeam = Auth::user()->currentTeam;

        $lease = Lease::updateOrCreate(
            ['id' => $this->leaseId],
            [
                'team_id' => $currentTeam->id,
                'room_id' => $this->room_id,
                'customer_id' => $this->customer_id,
                'start_date' => $this->start_date,
                'end_date' => $this->end_date,
                'monthly_rent' => $this->monthly_rent,
                'payment_status' => $this->payment_status,
                'status' => $this->status,
            ]
        );

        Auth::user()->notify(new TaskDueNotification(
            new \App\Models\Task(), // Opsional / sesuaikan pesan
            "Kontrak sewa baru dibuat untuk Kamar {$lease->room->room_number} (Penyewa: {$lease->tenant->name})."
        ));

        // Update status kamar menjadi occupied jika sewa aktif
        if ($this->status === 'active') {
            Room::where('id', $this->room_id)->update(['status' => 'occupied']);
        }

        session()->flash('message', $this->leaseId ? 'Kontrak sewa berhasil diperbarui.' : 'Kontrak sewa baru berhasil dibuat.');
        $this->closeModal();
    }

    public function render()
    {
        $currentTeam = Auth::user()->currentTeam;

        $availableRooms = Room::where('team_id', $currentTeam->id)->orderBy('room_number')->get();
        $tenants = Customer::where('team_id', $currentTeam->id)->orderBy('name')->get();

        $leases = Lease::where('team_id', $currentTeam->id)
            ->with(['room.property', 'tenant'])
            ->when($this->search, function ($q) {
                $q->whereHas('tenant', fn($t) => $t->where('name', 'like', '%' . $this->search . '%'))
                  ->orWhereHas('room', fn($r) => $r->where('room_number', 'like', '%' . $this->search . '%'));
            })
            ->when($this->statusFilter, fn($q) => $q->where('payment_status', $this->statusFilter))
            ->latest()
            ->paginate(10);

        return view('livewire.leases.index', [
            'leases' => $leases,
            'availableRooms' => $availableRooms,
            'tenants' => $tenants,
        ])->layout('layouts.app');
    }
}