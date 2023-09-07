<!-- Accountnumber Field -->
<div class="form-group col-sm-6">
    {!! Form::label('AccountNumber', 'Accountnumber:') !!}
    {!! Form::text('AccountNumber', null, ['class' => 'form-control','maxlength' => 255,'maxlength' => 255]) !!}
</div>

<!-- Userid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('UserId', 'Userid:') !!}
    {!! Form::text('UserId', null, ['class' => 'form-control','maxlength' => 255,'maxlength' => 255]) !!}
</div>

<!-- Status Field -->
<div class="form-group col-sm-6">
    {!! Form::label('Status', 'Status:') !!}
    {!! Form::text('Status', null, ['class' => 'form-control','maxlength' => 255,'maxlength' => 255]) !!}
</div>

<!-- Datesent Field -->
<div class="form-group col-sm-6">
    {!! Form::label('DateSent', 'Datesent:') !!}
    {!! Form::text('DateSent', null, ['class' => 'form-control','id'=>'DateSent']) !!}
</div>

@push('page_scripts')
    <script type="text/javascript">
        $('#DateSent').datetimepicker({
            format: 'YYYY-MM-DD HH:mm:ss',
            useCurrent: true,
            sideBySide: true
        })
    </script>
@endpush

<!-- Notes Field -->
<div class="form-group col-sm-6">
    {!! Form::label('Notes', 'Notes:') !!}
    {!! Form::text('Notes', null, ['class' => 'form-control','maxlength' => 1000,'maxlength' => 1000]) !!}
</div>