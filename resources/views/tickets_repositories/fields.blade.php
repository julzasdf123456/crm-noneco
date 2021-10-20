<!-- Name Field -->
<div class="form-group col-sm-6">
    {!! Form::label('Name', 'Name:') !!}
    {!! Form::text('Name', null, ['class' => 'form-control','maxlength' => 600,'maxlength' => 600]) !!}
</div>

<!-- Description Field -->
<div class="form-group col-sm-6">
    {!! Form::label('Description', 'Description:') !!}
    {!! Form::text('Description', null, ['class' => 'form-control','maxlength' => 1000,'maxlength' => 1000]) !!}
</div>

<!-- Parentticket Field -->
<div class="form-group col-sm-6">
    {!! Form::label('ParentTicket', 'Parentticket:') !!}
    {!! Form::text('ParentTicket', null, ['class' => 'form-control','maxlength' => 255,'maxlength' => 255]) !!}
</div>

<!-- Type Field -->
<div class="form-group col-sm-6">
    {!! Form::label('Type', 'Type:') !!}
    {!! Form::text('Type', null, ['class' => 'form-control','maxlength' => 255,'maxlength' => 255]) !!}
</div>

<!-- Kpscategory Field -->
<div class="form-group col-sm-6">
    {!! Form::label('KPSCategory', 'Kpscategory:') !!}
    {!! Form::text('KPSCategory', null, ['class' => 'form-control','maxlength' => 255,'maxlength' => 255]) !!}
</div>

<!-- Kpsissue Field -->
<div class="form-group col-sm-6">
    {!! Form::label('KPSIssue', 'Kpsissue:') !!}
    {!! Form::text('KPSIssue', null, ['class' => 'form-control','maxlength' => 255,'maxlength' => 255]) !!}
</div>