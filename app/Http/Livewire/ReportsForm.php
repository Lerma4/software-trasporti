<?php

namespace App\Http\Livewire;

use App\Models\Report;
use App\Models\Truck;
use Livewire\Component;

class ReportsForm extends Component
{
    public $trucks;

    public $truck, $text;

    public $uploated = NULL;

    protected $rules = [
        'truck' => 'required',
        'text' => 'required|min:5|max:10000',
    ];

    public function mount()
    {
        $this->trucks = Truck::where('companyId', auth()->user()->companyId)
            ->where('group', auth()->user()->group)
            ->get();
    }

    public function render()
    {
        return view('livewire.reports-form');
    }

    public function submit()
    {
        $this->validate();

        Report::create([
            'truck_id' => $this->truck,
            'user_id' => auth()->user()->id,
            'text' => $this->text,
            'companyId' => auth()->user()->companyId,
        ]);

        $this->reset([
            'truck',
            'text'
        ]);

        $this->uploated = true;

        session()->flash('message', __('Successful operation!'));
    }
}
