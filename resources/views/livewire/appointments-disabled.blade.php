<div>
    <div class="row m-4">
        <button class="btn text-white btn-info btn-sm" wire:click.prevent="add()">Add Time</button>
    </div>
    <div class="w-100"></div>
    @if (session()->has('timeMessage'))
        <div class="alert alert-success">
            {{ session('timeMessage') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
        @foreach($fromArray as $ikey => $ivalue)
            <div class="row col-md-12">
                <div class="col-md-3">
                    <div class="form-group">
                        <select name="day_of_week[]"
                                value="{{$dayOfWeekArray[$ikey]}}"
                                wire:change="setDayOfWeekValue({{$ikey}}, $event.target.value)"
                                @if(!$dayOfWeekArray[$ikey] || in_array($ikey, $duplicatesArray))
                                    style="border: 1px solid red; color: red"
                                @endif
                                class="form-select form-control">
                            <option value="">Select day of week</option>
                            @foreach ($daysOfWeek as $ind => $day)
                                <?php $selected = ($day === $dayOfWeekArray[$ikey] ? 'selected="selected"' : ''); ?>
                                <option value="{{$day}}" {{$selected}}>{{$day}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <input name="from[]"
                               type="time"
                               value="{{$ivalue}}"
                               wire:change="setFromValue({{$ikey}}, $event.target.value)"
                               @if(in_array($ikey, $keyArray) || in_array($ikey, $duplicatesArray))
                                    style="border: 1px solid red; color: red"
                               @endif
                               class="form-control"/>
                    </div>
                </div> -
                <div class="col-md-3">
                    <div class="form-group">
                        <input name="to[]"
                               type="time"
                               value="{{$toArray[$ikey]}}"
                               wire:change="setToValue({{$ikey}}, $event.target.value)"
{                              @if(in_array($ikey, $keyArray) || in_array($ikey, $duplicatesArray))
                                    style="border: 1px solid red; color: red"
                               @endif
                               class="form-control"/>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <button class="form-control btn btn-danger btn-sm" wire:click.prevent="remove({{$ikey}})">Remove Time</button>
                    </div>
                </div>
            </div>
            <div class="w-100"></div>
        @endforeach
    <br>
    <?php $disabled = (!$isChanged ? 'disabled="disabled"' : ''); ?>
    <div class="m-4">
        <button type="button" wire:click.prevent="store()" {{$disabled}} class="btn btn-success btn-sm">Save Time</button>
        <button type="button" wire:click.prevent="delete()" class="btn btn-danger btn-sm">Delete Test Type with Time</button>
    </div>
</div>
