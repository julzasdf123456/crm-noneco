<!-- Eventtitle Field -->
<div class="form-group col-sm-12">
    {!! Form::label('EventTitle', 'Event Title:') !!}
    {!! Form::text('EventTitle', null, ['class' => 'form-control','maxlength' => 300,'maxlength' => 300]) !!}
</div>

<!-- Eventdescription Field -->
<div class="form-group col-sm-12">
    {!! Form::label('EventDescription', 'Event Short Description:') !!}
    <textarea name="EventDescription" id="EventDescription" cols="30" rows="3" class="form-control"></textarea>
</div>

<!-- Eventstart Field -->
<div class="form-group col-sm-6">
    {!! Form::label('EventStart', 'Event Start Date/Time:') !!}
    {!! Form::text('EventStart', null, ['class' => 'form-control','id'=>'EventStart']) !!}
</div>

@push('page_scripts')
    <script type="text/javascript">
        $('#EventStart').datetimepicker({
            format: 'YYYY-MM-DD HH:mm',
            useCurrent: true,
            sideBySide: true
        })
    </script>
@endpush

<!-- Eventend Field -->
<div class="form-group col-sm-6">
    {!! Form::label('EventEnd', 'Event End Date/Time::') !!}
    {!! Form::text('EventEnd', null, ['class' => 'form-control','id'=>'EventEnd']) !!}
</div>

@push('page_scripts')
    <script type="text/javascript">
        $('#EventEnd').datetimepicker({
            format: 'YYYY-MM-DD HH:mm',
            useCurrent: true,
            sideBySide: true
        })
    </script>
@endpush

<!-- Registrationstart Field -->
<div class="form-group col-sm-6">
    {!! Form::label('RegistrationStart', 'Registration Start:') !!}
    {!! Form::text('RegistrationStart', null, ['class' => 'form-control','id'=>'RegistrationStart']) !!}
</div>

@push('page_scripts')
    <script type="text/javascript">
        $('#RegistrationStart').datetimepicker({
            format: 'YYYY-MM-DD HH:mm',
            useCurrent: true,
            sideBySide: true
        })
    </script>
@endpush

<!-- Registrationend Field -->
<div class="form-group col-sm-6">
    {!! Form::label('RegistrationEnd', 'Registration End:') !!}
    {!! Form::text('RegistrationEnd', null, ['class' => 'form-control','id'=>'RegistrationEnd']) !!}
</div>

@push('page_scripts')
    <script type="text/javascript">
        $('#RegistrationEnd').datetimepicker({
            format: 'YYYY-MM-DD HH:mm',
            useCurrent: true,
            sideBySide: true
        })
    </script>
@endpush
