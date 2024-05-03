<?php

namespace App\Http\Livewire;

use App\Models\LocationTestTypeAppointmentDisabled;
use App\Models\TestType;
use Livewire\Component;

class TestTypeAppointmentsDisabled extends Component
{
    public $location;
    public $testTypesIds;
    public $testTypesNames;
    public $indexTestTypes;
    public $testTypeDisabledAppointments = [];
    public $from = [];
    public $to = [];

    protected $listeners = [
        'setValue' => 'setValue',
        'removeTestType' => 'removeTestType',
    ];

    public function add()
    {
        array_push($this->testTypeDisabledAppointments, 0);
    }

    public function remove($i)
    {
        unset($this->testTypeDisabledAppointments[$i]);
    }

    public function setValue($key, $value)
    {
        $this->testTypeId = $value;
        $this->testTypeDisabledAppointments[$key] = $value;
    }

    public function mount($location)
    {
        $this->location = $location->id;
        $this->testTypesIds = TestType::get()->pluck('id')->toArray();
        $this->testTypesNames = TestType::get()->pluck('name')->toArray();
        $testTypesDisabledAppointments = LocationTestTypeAppointmentDisabled::where('location_id', $this->location)
            ->pluck('test_type_id')->toArray();
        $this->testTypeDisabledAppointments = array_unique($testTypesDisabledAppointments);
        $this->indexTestTypes = count($this->testTypeDisabledAppointments);
    }

    public function render()
    {
        return view('livewire.test-type-appointments-disabled', ['location' => $this->location]);
    }

    public function removeTestType($key)
    {
        unset($this->testTypeDisabledAppointments[$key[0]]);
        session()->flash('message', 'Disabled appointment time for the test type is deleted successfully!');
    }
}
