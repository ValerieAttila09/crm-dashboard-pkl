<?php

namespace App\Livewire\Leases;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Lease;
use App\Models\Room;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';

    // Form Modal Fields
    public $isModalOpen = false;
    public $leaseId = null;
    public $room_id, $customer_id, $start_date, $end_date, $monthly_rent = 0, $payment_status = 'unpaid', $status = 'active';

    // State Tambah Tenant Baru secara Inline
    public $isCreatingTenant = false;
    public $new_tenant_name = '';
    public $new_tenant_email = '';
    public $new_tenant_phone = '';

    protected function rules()
    {
        return [
            'room_id' => 'required|exists:rooms,id',
            'customer_id' => 'required|exists:customers,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'monthly_rent' => 'required|numeric|min:0',
            'payment_status' => 'required|in:paid,unpaid,overdue',
            'status' => 'required|in:active,ended,cancelled',
        ];
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
        $this->isCreatingTenant = false;
        $this->resetValidation();
    }

    // Otomatis set monthly_rent saat Kamar dipilih
    public function updatedRoomId($value)
    {
        if ($value) {
            $room = Room::find($value);
            if ($room) {
                $this->monthly_rent = $room->price_per_month;
            }
        }
    }

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
            'new_tenant_email' => 'required|email|max:255|unique:customers,email',
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

        $this->customer_id = $tenant->id;
        $this->isCreatingTenant = false;
    }

    public function store()
    {
        $this->validate();
        $currentTeam = Auth::user()->currentTeam;

        // 1. Simpan / Perbarui Kontrak Sewa
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

        // 2. Otomatis kunci status kamar menjadi 'occupied' jika sewa aktif
        if ($this->status === 'active') {
            Room::where('id', $this->room_id)->update(['status' => 'occupied']);
        }

        session()->flash('message', $this->leaseId ? 'Kontrak sewa berhasil diperbarui.' : 'Kontrak sewa baru berhasil dibuat.');
        $this->closeModal();
    }

    public function render()
    {
        $currentTeam = Auth::user()->currentTeam;

        // Ambil kamar yang berstatus 'available' (atau kamar dari kontrak aktif yang sedang di-edit)
        $availableRooms = Room::where('team_id', $currentTeam->id)
            ->where(function ($q) {
                $q->where('status', 'available');
                if ($this->room_id) {
                    $q->orWhere('id', $this->room_id);
                }
            })
            ->orderBy('room_number')
            ->get();

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

        // 1. Ubah status pembayaran secara instan (Paid / Unpaid / Overdue)
    public function togglePaymentStatus($leaseId, $newStatus)
    {
        $currentTeam = Auth::user()->currentTeam;
        $lease = Lease::where('team_id', $currentTeam->id)->findOrFail($leaseId);

        $lease->update(['payment_status' => $newStatus]);

        session()->flash('message', "Status pembayaran untuk Kamar {$lease->room->room_number} berhasil diperbarui.");
    }

    // 2. Akhiri Masa Sewa / Terminate Kontrak
    public function terminateLease($leaseId)
    {
        $currentTeam = Auth::user()->currentTeam;
        $lease = Lease::where('team_id', $currentTeam->id)->findOrFail($leaseId);

        // Ubah status kontrak menjadi ended
        $lease->update(['status' => 'ended']);

        // Otomatis kembalikan status kamar menjadi 'available'
        $lease->room()->update(['status' => 'available']);

        session()->flash('message', "Kontrak sewa telah diakhiri. Kamar {$lease->room->room_number} kini berstatus Available.");
    }

    // 3. Generate Link WhatsApp Pengingat Tagihan
    public function getWaReminderUrl($leaseId)
    {
        $currentTeam = Auth::user()->currentTeam;
        $lease = Lease::where('team_id', $currentTeam->id)->with(['tenant', 'room.property'])->findOrFail($leaseId);

        $phone = preg_replace('/[^0-9]/', '', $lease->tenant->phone ?? '');
        
        // Ubah awalan 08 menjadi 62 untuk format internasional WhatsApp
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        $formattedRent = 'Rp ' . number_format($lease->monthly_rent, 0, ',', '.');
        $dueDate = \Carbon\Carbon::parse($lease->end_date)->translatedFormat('d F Y');

        $message = "Halo *{$lease->tenant->name}*,\n\n"
                . "Berikut adalah pengingat tagihan sewa untuk unit Anda:\n"
                . "🏢 *Properti*: {$lease->room->property->name}\n"
                . "🏠 *Kamar*: {$lease->room->room_number}\n"
                . "💰 *Total Tagihan*: {$formattedRent}\n"
                . "📅 *Tenggat Waktu*: {$dueDate}\n\n"
                . "Mohon untuk melakukan konfirmasi pembayaran setelah transfer. Terima kasih!";

        return "https://wa.me/{$phone}?text=" . urlencode($message);
    }
}