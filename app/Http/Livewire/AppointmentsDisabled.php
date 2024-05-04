<?php

namespace App\Http\Livewire;

use App\Models\Location;
use App\Models\LocationTestTypeAppointmentDisabled;
use App\Models\TestType;
use Carbon\Carbon;
use Livewire\Component;
use Slack;

class AppointmentsDisabled extends Component
{
    public $location;
    public $testTypeId;
    public $from;
    public $fromArray = [];
    public $toArray = [];
    public $keyVal;
    public $to;
    public $isChanged = false;
    public $keyArray = [];

    public $dbRecords = [];
    public $savedKeys = [];
    public $removedRecords = [];
    public $removedIds = [];

    public $dayOfWeekArray = [];
    public $daysOfWeek = [];
    public $emptyDayArray = [];

    public $duplicatesArray = [];
    public $savedItemsKeys = [];

    protected $listeners = [
        'save' => 'store',
        'delete' => 'delete',
    ];

    public function add()
    {
        array_push($this->toArray, '08:30:00');
        array_push($this->fromArray, '08:00:00');
        array_push($this->dayOfWeekArray, 'Mon');

        $this->isChanged = true;
    }

    public function remove($ii)
    {
        unset($this->toArray[$ii]);
        unset($this->fromArray[$ii]);
        unset($this->dayOfWeekArray[$ii]);
        $this->isChanged = true;
    }

    public function mount($location, $testTypeId, $keyVal)
    {
        $this->location = $location;
        $this->testTypeId = $testTypeId;
        $this->keyVal = $keyVal;
        $this->toArray = LocationTestTypeAppointmentDisabled::where('location_id', $this->location)
            ->where('test_type_id', $this->testTypeId)->orderBy('id')
            ->pluck('to')->toArray();
        $this->fromArray = LocationTestTypeAppointmentDisabled::where('location_id', $this->location)
            ->where('test_type_id', $this->testTypeId)->orderBy('id')
            ->pluck('from')->toArray();

        $this->dayOfWeekArray = LocationTestTypeAppointmentDisabled::where('location_id', $this->location)
            ->where('test_type_id', $this->testTypeId)->orderBy('id')
            ->pluck('day_of_week')->toArray();

        $this->dbRecords = json_decode(json_encode(LocationTestTypeAppointmentDisabled::where('location_id', $this->location)
            ->where('test_type_id', $this->testTypeId)->get()));

        $this->daysOfWeek = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
    }

    public function setDayOfWeekValue($key, $value)
    {
        $this->dayOfWeekArray[$key] = $value;
        $this->isChanged = true;
    }

    public function setToValue($key, $value)
    {
        $this->toArray[$key] = $value;
        $this->isChanged = true;
    }

    public function setFromValue($key, $value)
    {
        $this->fromArray[$key] = $value;
        $this->isChanged = true;
    }

    public function render()
    {
        return view('livewire.appointments-disabled');
    }

    private function isInterval()
    {
        $key = -1;
        foreach ($this->fromArray as $k => $value) {
            $timeDifference = Carbon::parse($this->fromArray[$k])->format('H:i:s') > Carbon::parse($this->toArray[$k])->format('H:i:s');
            if ($timeDifference) {
                $this->keyArray[] = $k;
                $key = $k;
                break;
            }
        }

        return $key;
    }

    private function isInArray($val, $array, $str)
    {
        $flag = -1;
        foreach ($array as $k => $v) {
            if (Carbon::parse($array[$k])->format('H:i:s') === Carbon::parse($val->$str)->format('H:i:s')
            && $val->day_of_week === $this->dayOfWeekArray[$k]) {
                $flag = $k;
                break;
            }
        }

        return $flag;
    }

    private function isRecordInArrays($dbRecord)
    {
        $flag = -1;
        $resultKey = $this->isInArray($dbRecord, $this->toArray, 'to');
        if ($resultKey !== -1 && $this->isInArray($dbRecord, $this->fromArray, 'from') !== -1) {
            $flag = $resultKey;
        }
        return $flag;
    }

    private function checkForDuplicates()
    {
        $flag = -1;
        foreach ($this->dayOfWeekArray as $key => $value) {
            if ($this->hasDuplicates($key)) {
                $flag = $key;
                $this->duplicatesArray[] = $key;
                break;
            }
        }

        return $flag;
    }

    private function hasDuplicates($key)
    {
        $flag = false;
        foreach ($this->dayOfWeekArray as $k => $value) {
            if ($k !== $key && $this->dayOfWeekArray[$k] === $this->dayOfWeekArray[$key] &&
                Carbon::parse($this->fromArray[$k])->format('H:i:s') === Carbon::parse($this->fromArray[$key])->format('H:i:s') &&
                Carbon::parse($this->toArray[$k])->format('H:i:s') === Carbon::parse($this->toArray[$key])->format('H:i:s')) {
                $flag = true;
                break;
            }
        }

        return $flag;
    }

    private function check()
    {
        foreach ($this->dbRecords as $dbRecord) {
            $key = $this->isRecordInArrays($dbRecord);
            if ($key !== -1) {
                $this->savedKeys[] = $key;
                $this->savedItemsKeys[] = $key;
            } else {
                $this->removedRecords[] = $dbRecord;
                $this->removedIds[] = $dbRecord->id;
            }
        }
    }

    private function isEmpty()
    {
        $result = -1;
        foreach ($this->dayOfWeekArray as $key => $value) {
            if (!$value) {
                $result = $key;
                $this->emptyDayArray[] = $key;
                break;
            }
        }

        return $result;
    }

    public function store()
    {
        $result = $this->isEmpty();
        if ($result !== -1) {
            session()->flash('error', 'Disabled appointment day of week at row ' . ($result + 1) . ' is empty!');

            return;
        }

        $result = $this->checkForDuplicates();
        if ($result !== -1) {
            session()->flash('error', 'Disabled appointment at row ' . ($result + 1) . ' is a duplicate!');

            return;
        }

        $result = $this->isInterval();
        if ($result !== -1) {
            session()->flash('error', 'Disabled appointment time at row ' . ($result + 1) . ' is not interval! Time "from" is grater than time "to"!');

            return;
        }

        $this->check();
        $location = Location::find($this->location);
        $testType = TestType::find($this->testTypeId);
        if (!empty($this->removedIds)) {
            LocationTestTypeAppointmentDisabled::whereIn('id', $this->removedIds)->delete();
            foreach ($this->removedRecords as $record) {
                // Slack::send('The disabled appointment time on ' . $record['day_of_week'] . ' from ' . $record['from'] . ' to ' . $record['to'] . ' for test type ' . $testType->name
                //     . ' for location ' . $location->name . ' for organization ' . $location->organization->name . ' is deleted!');
            }
        }
        foreach ($this->toArray as $key => $value) {
            if (!in_array($key, $this->savedItemsKeys)) {
                LocationTestTypeAppointmentDisabled::create(
                    [
                        'location_id' => $this->location,
                        'test_type_id' => $this->testTypeId,
                        'day_of_week' => $this->dayOfWeekArray[$key],
                        'from' => $this->fromArray[$key],
                        'to' => $this->toArray[$key],
                    ]
                );
                $this->savedItemsKeys[] = $key;
            }
            if (!in_array($key, $this->savedKeys)) {
                // Slack::send('The disabled appointment time on ' . $this->dayOfWeekArray[$key] . ' from ' . $this->fromArray[$key] . ' to ' . $this->toArray[$key] .
                //     ' for test type ' . $testType->name . ' for location ' . $location->name . ' for organization ' . $location->organization->name . ' is added!');
            }
        }
        $this->savedKeys = [];
        $this->removedRecords = [];
        $this->dbRecords = json_decode(json_encode(LocationTestTypeAppointmentDisabled::where('location_id', $this->location)
            ->where('test_type_id', $this->testTypeId)->get()));
        $this->isChanged = false;
        session()->flash('timeMessage', 'Disabled appointment time is saved successfully!');
    }

    public function delete()
    {
        $location = Location::find($this->location);
        $testType = TestType::find($this->testTypeId);
        $this->removedRecords = json_decode(json_encode(LocationTestTypeAppointmentDisabled::where('location_id', $this->location)
            ->where('test_type_id', $this->testTypeId)->get()));
        LocationTestTypeAppointmentDisabled::where('location_id', $this->location)
            ->where('test_type_id', $this->testTypeId)->delete();
        foreach ($this->removedRecords as $record) {
            // Slack::send('The disabled appointment time on ' . $record->day_of_week . ' from ' . $record->from . ' to ' . $record->to . ' for test type ' . $testType->name
            //     . ' for location ' . $location->name . ' for organization ' . $location->organization->name . ' is deleted!');
        }
        $this->emit('removeTestType', [$this->keyVal]);

        session()->flash('timeMessage', 'Disabled appointment time for the test type is deleted successfully!');
    }
}
