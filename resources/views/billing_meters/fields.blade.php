<!-- Serviceaccountid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('ServiceAccountId', 'Serviceaccountid:') !!}
    {!! Form::text('ServiceAccountId', null, ['class' => 'form-control','maxlength' => 120,'maxlength' => 120]) !!}
</div>

<!-- Serialnumber Field -->
<div class="form-group col-sm-6">
    {!! Form::label('SerialNumber', 'Serialnumber:') !!}
    {!! Form::text('SerialNumber', null, ['class' => 'form-control','maxlength' => 100,'maxlength' => 100]) !!}
</div>

<!-- Sealnumber Field -->
<div class="form-group col-sm-6">
    {!! Form::label('SealNumber', 'Sealnumber:') !!}
    {!! Form::text('SealNumber', null, ['class' => 'form-control','maxlength' => 120,'maxlength' => 120]) !!}
</div>

<!-- Brand Field -->
<div class="form-group col-sm-6">
    {!! Form::label('Brand', 'Brand:') !!}
    {!! Form::text('Brand', null, ['class' => 'form-control','maxlength' => 180,'maxlength' => 180]) !!}
</div>

<!-- Model Field -->
<div class="form-group col-sm-6">
    {!! Form::label('Model', 'Model:') !!}
    {!! Form::text('Model', null, ['class' => 'form-control','maxlength' => 180,'maxlength' => 180]) !!}
</div>

<!-- Multiplier Field -->
<div class="form-group col-sm-6">
    {!! Form::label('Multiplier', 'Multiplier:') !!}
    {!! Form::text('Multiplier', null, ['class' => 'form-control','maxlength' => 10,'maxlength' => 10]) !!}
</div>

<!-- Status Field -->
<div class="form-group col-sm-6">
    {!! Form::label('Status', 'Status:') !!}
    {!! Form::text('Status', null, ['class' => 'form-control','maxlength' => 60,'maxlength' => 60]) !!}
</div>

<!-- Connectiondate Field -->
<div class="form-group col-sm-6">
    {!! Form::label('ConnectionDate', 'Connectiondate:') !!}
    {!! Form::text('ConnectionDate', null, ['class' => 'form-control','id'=>'ConnectionDate']) !!}
</div>

@push('page_scripts')
    <script type="text/javascript">
        $('#ConnectionDate').datetimepicker({
            format: 'YYYY-MM-DD HH:mm:ss',
            useCurrent: true,
            sideBySide: true
        })
    </script>
@endpush

<!-- Latestreadingdate Field -->
<div class="form-group col-sm-6">
    {!! Form::label('LatestReadingDate', 'Latestreadingdate:') !!}
    {!! Form::text('LatestReadingDate', null, ['class' => 'form-control','id'=>'LatestReadingDate']) !!}
</div>

@push('page_scripts')
    <script type="text/javascript">
        $('#LatestReadingDate').datetimepicker({
            format: 'YYYY-MM-DD HH:mm:ss',
            useCurrent: true,
            sideBySide: true
        })
    </script>
@endpush

<!-- Datedisconnected Field -->
<div class="form-group col-sm-6">
    {!! Form::label('DateDisconnected', 'Datedisconnected:') !!}
    {!! Form::text('DateDisconnected', null, ['class' => 'form-control','id'=>'DateDisconnected']) !!}
</div>

@push('page_scripts')
    <script type="text/javascript">
        $('#DateDisconnected').datetimepicker({
            format: 'YYYY-MM-DD HH:mm:ss',
            useCurrent: true,
            sideBySide: true
        })
    </script>
@endpush

<!-- Datetransfered Field -->
<div class="form-group col-sm-6">
    {!! Form::label('DateTransfered', 'Datetransfered:') !!}
    {!! Form::text('DateTransfered', null, ['class' => 'form-control','id'=>'DateTransfered']) !!}
</div>

@push('page_scripts')
    <script type="text/javascript">
        $('#DateTransfered').datetimepicker({
            format: 'YYYY-MM-DD HH:mm:ss',
            useCurrent: true,
            sideBySide: true
        })
    </script>
@endpush