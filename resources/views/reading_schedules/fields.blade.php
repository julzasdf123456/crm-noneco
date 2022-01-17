<!-- Areacode Field -->
<div class="form-group col-sm-6">
    {!! Form::label('AreaCode', 'Areacode:') !!}
    {!! Form::text('AreaCode', null, ['class' => 'form-control','maxlength' => 255,'maxlength' => 255]) !!}
</div>

<!-- Groupcode Field -->
<div class="form-group col-sm-6">
    {!! Form::label('GroupCode', 'Groupcode:') !!}
    {!! Form::text('GroupCode', null, ['class' => 'form-control','maxlength' => 255,'maxlength' => 255]) !!}
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

<!-- Scheduleddate Field -->
<div class="form-group col-sm-6">
    {!! Form::label('ScheduledDate', 'Scheduleddate:') !!}
    {!! Form::text('ScheduledDate', null, ['class' => 'form-control','id'=>'ScheduledDate']) !!}
</div>

@push('page_scripts')
    <script type="text/javascript">
        $('#ScheduledDate').datetimepicker({
            format: 'YYYY-MM-DD HH:mm:ss',
            useCurrent: true,
            sideBySide: true
        })
    </script>
@endpush

<!-- Meterreader Field -->
<div class="form-group col-sm-6">
    {!! Form::label('MeterReader', 'Meterreader:') !!}
    {!! Form::text('MeterReader', null, ['class' => 'form-control','maxlength' => 255,'maxlength' => 255]) !!}
</div>