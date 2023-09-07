<!-- Demandletterid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('DemandLetterId', 'Demandletterid:') !!}
    {!! Form::text('DemandLetterId', null, ['class' => 'form-control','maxlength' => 255,'maxlength' => 255]) !!}
</div>

<!-- Serviceperiod Field -->
<div class="form-group col-sm-6">
    {!! Form::label('ServicePeriod', 'Serviceperiod:') !!}
    {!! Form::text('ServicePeriod', null, ['class' => 'form-control','id'=>'ServicePeriod']) !!}
</div>

@push('page_scripts')
    <script type="text/javascript">
        $('#ServicePeriod').datetimepicker({
            format: 'YYYY-MM-DD HH:mm:ss',
            useCurrent: true,
            sideBySide: true
        })
    </script>
@endpush

<!-- Accountnumber Field -->
<div class="form-group col-sm-6">
    {!! Form::label('AccountNumber', 'Accountnumber:') !!}
    {!! Form::text('AccountNumber', null, ['class' => 'form-control','maxlength' => 255,'maxlength' => 255]) !!}
</div>

<!-- Netamount Field -->
<div class="form-group col-sm-6">
    {!! Form::label('NetAmount', 'Netamount:') !!}
    {!! Form::number('NetAmount', null, ['class' => 'form-control']) !!}
</div>

<!-- Surcharge Field -->
<div class="form-group col-sm-6">
    {!! Form::label('Surcharge', 'Surcharge:') !!}
    {!! Form::number('Surcharge', null, ['class' => 'form-control']) !!}
</div>

<!-- Interest Field -->
<div class="form-group col-sm-6">
    {!! Form::label('Interest', 'Interest:') !!}
    {!! Form::number('Interest', null, ['class' => 'form-control']) !!}
</div>

<!-- Totalamountdue Field -->
<div class="form-group col-sm-6">
    {!! Form::label('TotalAmountDue', 'Totalamountdue:') !!}
    {!! Form::number('TotalAmountDue', null, ['class' => 'form-control']) !!}
</div>

<!-- Notes Field -->
<div class="form-group col-sm-6">
    {!! Form::label('Notes', 'Notes:') !!}
    {!! Form::text('Notes', null, ['class' => 'form-control','maxlength' => 1000,'maxlength' => 1000]) !!}
</div>

<!-- Status Field -->
<div class="form-group col-sm-6">
    {!! Form::label('Status', 'Status:') !!}
    {!! Form::text('Status', null, ['class' => 'form-control','maxlength' => 255,'maxlength' => 255]) !!}
</div>