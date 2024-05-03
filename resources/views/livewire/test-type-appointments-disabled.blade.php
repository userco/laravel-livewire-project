<div>
    <form>
       @csrf
        <div class="row m-4">
            <button class="btn text-white btn-info btn-sm" wire:click.prevent="add()">Add Test Type</button>
        </div>
        @if (session()->has('message'))
            <div class="alert alert-success">
                {{ session('message') }}
            </div>
        @endif
        <?php
            $idArray = [];
        ?>
        @foreach($testTypeDisabledAppointments as $key => $value)
          <div class="form-inline col-md-12">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="test-type.{{$key}}"><b>Select test type</b></label>
                    </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                      <select name="test_type[]"
                              class="form-select form-control col-md-5"
                              wire:change="setValue({{$key}}, $event.target.value)"
                              id="test-type.{{$key}}"
                              wire:key="{{$key}}">
                          <option value="">Select test type</option>
                          @foreach ($testTypesIds as $index => $testType)
                              @if (!in_array($testType, $idArray))
                                  <?php $selected = ($testType === $value ? 'selected="selected"' : ''); ?>
                                  <option value="{{$testType}}" {{$selected}}>{{$testTypesNames[$index]}}</option>
                              @endif
                          @endforeach
                      </select>
                  </div>
              </div>
            </div>
            <br>
        @php
             if (isset($testTypeDisabledAppointments[$key]) && $testTypeDisabledAppointments[$key]){
                $val = $testTypeDisabledAppointments[$key];
        @endphp
        <livewire:appointmentsDisabled :location="$location" :testTypeId="$val"  :keyVal="$key" :wire:key="time().$value"/>
        @php
            }
        @endphp
            <hr>
            <?php
                $idArray[] = $value;
            ?>
        @endforeach
    </form>
</div>
