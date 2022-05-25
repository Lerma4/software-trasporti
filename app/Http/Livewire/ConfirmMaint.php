<?php

namespace App\Http\Livewire;

use App\Models\MaintAlreadyDone;
use App\Models\MaintStillToDo;
use App\Models\Truck;
use Livewire\Component;

class ConfirmMaint extends Component
{
    public $trucks;
    public $maint;
    public $maint_id;
    public $garage;
    public $km;
    public $price;
    public $notes;
    public $date;

    public $selectedTruck = null;
    public $uploated = null;

    protected $rules = [
        'maint_id' => 'required',
        'garage' => '',
        'date' => 'required',
        'km' => 'required',
        'price' => '',
        'notes' => '',
    ];

    public function mount()
    {
        $this->trucks = Truck::where('companyId', auth()->user()->companyId)
            ->where('group', auth()->user()->group)
            ->get();
        $this->maint = collect();
    }

    public function render()
    {
        return view('livewire.confirm-maint');
    }

    public function updatedSelectedTruck($id)
    {
        if ($id == '') $this->selectedTruck = null;

        if (isset($this->selectedTruck)) {
            $truck = Truck::findOrFail($id);

            $this->maint = MaintStillToDo::where('companyId', auth()->user()->companyId)
                ->where('plate', $truck->plate)
                ->get();

            $this->km = $truck->km;
        } else {
            $this->maint = '';
            $this->km = '';
        }
    }

    public function submit()
    {
        $this->validate();

        $maint = MaintStillToDo::findOrFail($this->maint_id);

        MaintAlreadyDone::create([
            'date' => $this->date,
            'plate' => $maint->plate,
            'type' => $maint->type,
            'km' => $this->km,
            'garage' => $this->garage,
            'price' => $this->price,
            'notes' => $this->notes,
            'companyId' => auth()->user()->companyId,
        ]);

        if ($maint->renew != NULL) {
            MaintStillToDo::create([
                'plate' => $maint->plate,
                'type' => $maint->type,
                'km' => $maint->renew,
                'renew' => $maint->renew,
                //'notes' => $maint->notes,
                'companyId' => auth()->user()->companyId,
            ]);
        }

        $maint->delete();

        $this->reset([
            'date',
            'km',
            'garage',
            'price',
            'notes'
        ]);

        $this->km = '';
        $this->maint = '';
        $this->selectedTruck = null;
        $this->uploated = true;

        session()->flash('message', __('Successful operation!'));
    }
}
