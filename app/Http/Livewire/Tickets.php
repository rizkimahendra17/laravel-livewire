<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\SupportTicket;


class Tickets extends Component
{
    public $active;


    protected $listeners = [
    
        'ticketSelected' => 'ticketSelected'
    ];

    public function ticketSelected($ticketId)
    {
        $this->active = $ticketId;
    }

    // protected $model = SupportTicket::class;


    public function render()
    {
        $tickets = SupportTicket::all();
        return view('livewire.tickets',[
            'tickets' => $tickets,
        ]);
    }
}