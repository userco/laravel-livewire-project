@extends('layouts.app')

@section('content')
    <div class="py-12">
        <div class="mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">    
				<div class="tab-pane fade show active">
					<h3 class="font-weight-bold text-center">{{ __('Disabled Test Type Appointments') }}</h3>
					<livewire:testTypeAppointmentsDisabled :location="$location" />
				</div>
            </div>
        </div>
    </div>
@endsection