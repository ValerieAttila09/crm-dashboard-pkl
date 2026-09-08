<?php

namespace App\Livewire\Public;

use Livewire\Component;

class Contact extends Component
{
    public $name = '';
    public $email = '';
    public $phone = '';
    public $subject = 'Inquiry Sewa Unit';
    public $message = '';

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email',
        'phone' => 'required|string',
        'subject' => 'required|string',
        'message' => 'required|string',
    ];

    public function submit()
    {
        $this->validate();

        // Format pesan pesan terstruktur ke WhatsApp Admin
        $text = "📩 *PESAN BARU DARI WEBSITE*\n\n"
              . "👤 *Nama*: {$this->name}\n"
              . "📧 *Email*: {$this->email}\n"
              . "📞 *No HP/WA*: {$this->phone}\n"
              . "📌 *Kategori*: {$this->subject}\n\n"
              . "💬 *Pesan/Pertanyaan*:\n{$this->message}";

        $adminPhone = '6281234567890'; // Ganti dengan nomor WA Admin/Sales
        $waUrl = "https://wa.me/{$adminPhone}?text=" . urlencode($text);

        $this->reset(['name', 'email', 'phone', 'message']);
        session()->flash('message', 'Pesan Anda telah siap dikirimkan ke tim Admin.');

        return redirect()->away($waUrl);
    }

    public function render()
    {
        return view('livewire.public.contact')->layout('layouts.guest');
    }
}