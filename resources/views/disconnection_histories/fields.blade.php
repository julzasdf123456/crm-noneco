<!-- Accountnumber Field -->
<div class="form-group col-sm-6">
    {!! Form::label('AccountNumber', 'Accountnumber:') !!}
    {!! Form::text('AccountNumber', null, ['class' => 'form-control','maxlength' => 255,'maxlength' => 255]) !!}
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

<!-- Latitude Field -->
<div class="form-group col-sm-6">
    {!! Form::label('Latitude', 'Latitude:') !!}
    {!! Form::text('Latitude', null, ['class' => 'form-control','maxlength' => 255,'maxlength' => 255]) !!}
</div>

<!-- Longitude Field -->
<div class="form-group col-sm-6">
    {!! Form::label('Longitude', 'Longitude:') !!}
    {!! Form::text('Longitude', null, ['class' => 'form-control','maxlength' => 255,'maxlength' => 255]) !!}
</div>

<!-- Billid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('BillId', 'Billid:') !!}
    {!! Form::text('BillId', null, ['class' => 'form-control','maxlength' => 255,'maxlength' => 255]) !!}
</div>

<!-- Disconnectionpayment Field -->
<div class="form-group col-sm-6">
    {!! Form::label('DisconnectionPayment', 'Disconnectionpayment:') !!}
    {!! Form::text('DisconnectionPayment', null, ['class' => 'form-control','maxlength' => 255,'maxlength' => 255]) !!}
</div>

<!-- Status Field -->
<div class="form-group col-sm-6">
    {!! Form::label('Status', 'Status:') !!}
    {!! Form::text('Status', null, ['class' => 'form-control','maxlength' => 255,'maxlength' => 255]) !!}
</div>

<!-- Userid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('UserId', 'Userid:') !!}
    {!! Form::text('UserId', null, ['class' => 'form-control','maxlength' => 255,'maxlength' => 255]) !!}
</div>

<!-- Notes Field -->
<div class="form-group col-sm-6">
    {!! Form::label('Notes', 'Notes:') !!}
    {!! Form::text('Notes', null, ['class' => 'form-control','maxlength' => 255,'maxlength' => 255]) !!}
</div>