<!-- Accountnumber Field -->
<div class="form-group col-sm-6">
    {!! Form::label('AccountNumber', 'Accountnumber:') !!}
    {!! Form::text('AccountNumber', null, ['class' => 'form-control','maxlength' => 255,'maxlength' => 255]) !!}
</div>

<!-- Consumername Field -->
<div class="form-group col-sm-6">
    {!! Form::label('ConsumerName', 'Consumername:') !!}
    {!! Form::text('ConsumerName', null, ['class' => 'form-control','maxlength' => 500,'maxlength' => 500]) !!}
</div>

<!-- Town Field -->
<div class="form-group col-sm-6">
    {!! Form::label('Town', 'Town:') !!}
    {!! Form::text('Town', null, ['class' => 'form-control','maxlength' => 255,'maxlength' => 255]) !!}
</div>

<!-- Barangay Field -->
<div class="form-group col-sm-6">
    {!! Form::label('Barangay', 'Barangay:') !!}
    {!! Form::text('Barangay', null, ['class' => 'form-control','maxlength' => 255,'maxlength' => 255]) !!}
</div>

<!-- Sitio Field -->
<div class="form-group col-sm-6">
    {!! Form::label('Sitio', 'Sitio:') !!}
    {!! Form::text('Sitio', null, ['class' => 'form-control','maxlength' => 800,'maxlength' => 800]) !!}
</div>

<!-- Ticket Field -->
<div class="form-group col-sm-6">
    {!! Form::label('Ticket', 'Ticket:') !!}
    {!! Form::text('Ticket', null, ['class' => 'form-control','maxlength' => 255,'maxlength' => 255]) !!}
</div>

<!-- Reason Field -->
<div class="form-group col-sm-6">
    {!! Form::label('Reason', 'Reason:') !!}
    {!! Form::text('Reason', null, ['class' => 'form-control','maxlength' => 2000,'maxlength' => 2000]) !!}
</div>

<!-- Contactnumber Field -->
<div class="form-group col-sm-6">
    {!! Form::label('ContactNumber', 'Contactnumber:') !!}
    {!! Form::text('ContactNumber', null, ['class' => 'form-control','maxlength' => 100,'maxlength' => 100]) !!}
</div>

<!-- Reportedby Field -->
<div class="form-group col-sm-6">
    {!! Form::label('ReportedBy', 'Reportedby:') !!}
    {!! Form::text('ReportedBy', null, ['class' => 'form-control','maxlength' => 200,'maxlength' => 200]) !!}
</div>

<!-- Ornumber Field -->
<div class="form-group col-sm-6">
    {!! Form::label('ORNumber', 'Ornumber:') !!}
    {!! Form::text('ORNumber', null, ['class' => 'form-control','maxlength' => 255,'maxlength' => 255]) !!}
</div>

<!-- Ordate Field -->
<div class="form-group col-sm-6">
    {!! Form::label('ORDate', 'Ordate:') !!}
    {!! Form::text('ORDate', null, ['class' => 'form-control','id'=>'ORDate']) !!}
</div>

@push('page_scripts')
    <script type="text/javascript">
        $('#ORDate').datetimepicker({
            format: 'YYYY-MM-DD HH:mm:ss',
            useCurrent: true,
            sideBySide: true
        })
    </script>
@endpush

<!-- Geolocation Field -->
<div class="form-group col-sm-6">
    {!! Form::label('GeoLocation', 'Geolocation:') !!}
    {!! Form::text('GeoLocation', null, ['class' => 'form-control','maxlength' => 60,'maxlength' => 60]) !!}
</div>

<!-- Neighbor1 Field -->
<div class="form-group col-sm-6">
    {!! Form::label('Neighbor1', 'Neighbor1:') !!}
    {!! Form::text('Neighbor1', null, ['class' => 'form-control','maxlength' => 500,'maxlength' => 500]) !!}
</div>

<!-- Neighbor2 Field -->
<div class="form-group col-sm-6">
    {!! Form::label('Neighbor2', 'Neighbor2:') !!}
    {!! Form::text('Neighbor2', null, ['class' => 'form-control','maxlength' => 500,'maxlength' => 500]) !!}
</div>

<!-- Notes Field -->
<div class="form-group col-sm-6">
    {!! Form::label('Notes', 'Notes:') !!}
    {!! Form::text('Notes', null, ['class' => 'form-control','maxlength' => 2000,'maxlength' => 2000]) !!}
</div>

<!-- Status Field -->
<div class="form-group col-sm-6">
    {!! Form::label('Status', 'Status:') !!}
    {!! Form::text('Status', null, ['class' => 'form-control','maxlength' => 255,'maxlength' => 255]) !!}
</div>

<!-- Datetimedownloaded Field -->
<div class="form-group col-sm-6">
    {!! Form::label('DateTimeDownloaded', 'Datetimedownloaded:') !!}
    {!! Form::text('DateTimeDownloaded', null, ['class' => 'form-control','id'=>'DateTimeDownloaded']) !!}
</div>

@push('page_scripts')
    <script type="text/javascript">
        $('#DateTimeDownloaded').datetimepicker({
            format: 'YYYY-MM-DD HH:mm:ss',
            useCurrent: true,
            sideBySide: true
        })
    </script>
@endpush

<!-- Datetimelinemanarrived Field -->
<div class="form-group col-sm-6">
    {!! Form::label('DateTimeLinemanArrived', 'Datetimelinemanarrived:') !!}
    {!! Form::text('DateTimeLinemanArrived', null, ['class' => 'form-control','id'=>'DateTimeLinemanArrived']) !!}
</div>

@push('page_scripts')
    <script type="text/javascript">
        $('#DateTimeLinemanArrived').datetimepicker({
            format: 'YYYY-MM-DD HH:mm:ss',
            useCurrent: true,
            sideBySide: true
        })
    </script>
@endpush

<!-- Datetimelinemanexecuted Field -->
<div class="form-group col-sm-6">
    {!! Form::label('DateTimeLinemanExecuted', 'Datetimelinemanexecuted:') !!}
    {!! Form::text('DateTimeLinemanExecuted', null, ['class' => 'form-control','id'=>'DateTimeLinemanExecuted']) !!}
</div>

@push('page_scripts')
    <script type="text/javascript">
        $('#DateTimeLinemanExecuted').datetimepicker({
            format: 'YYYY-MM-DD HH:mm:ss',
            useCurrent: true,
            sideBySide: true
        })
    </script>
@endpush

<!-- Userid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('UserId', 'Userid:') !!}
    {!! Form::text('UserId', null, ['class' => 'form-control','maxlength' => 255,'maxlength' => 255]) !!}
</div>

<!-- Crewassigned Field -->
<div class="form-group col-sm-6">
    {!! Form::label('CrewAssigned', 'Crewassigned:') !!}
    {!! Form::text('CrewAssigned', null, ['class' => 'form-control','maxlength' => 255,'maxlength' => 255]) !!}
</div>