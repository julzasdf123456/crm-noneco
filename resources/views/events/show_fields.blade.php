<!-- Eventdescription Field -->
<div class="col-sm-12">
    {!! Form::label('EventDescription', 'Event Description:') !!}
    <p>{{ $events->EventDescription }}</p>
</div>

<!-- Eventstart Field -->
<div class="col-sm-12">
    {!! Form::label('EventStart', 'Eventstart:') !!}
    <p>{{ $events->EventStart !=null ? date('F d, Y h:i A', strtotime($events->EventStart)) : '' }}</p>
</div>

<!-- Eventend Field -->
<div class="col-sm-12">
    {!! Form::label('EventEnd', 'Event End:') !!}
    <p>{{ $events->EventEnd !=null ? date('F d, Y h:i A', strtotime($events->EventEnd)) : '' }}</p>
</div>

<!-- Registrationstart Field -->
<div class="col-sm-12">
    {!! Form::label('RegistrationStart', 'Registration Start:') !!}
    <p>{{ $events->RegistrationStart !=null ? date('F d, Y h:i A', strtotime($events->RegistrationStart)) : '' }}</p>
</div>

<!-- Registrationend Field -->
<div class="col-sm-12">
    {!! Form::label('RegistrationEnd', 'Registration End:') !!}
    <p>{{ $events->RegistrationEnd !=null ? date('F d, Y h:i A', strtotime($events->RegistrationEnd)) : '' }}</p>
</div>
