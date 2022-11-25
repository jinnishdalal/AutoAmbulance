<div class="card bg-none card-box">
    {{ Form::open(array('url' => 'estimations')) }}
    <div class="row">
        <div class="form-group col-6">
            {{ Form::label('client_id', __('Client'),['class'=>'form-control-label']) }}
            {{ Form::select('client_id', $client,null, array('class' => 'form-control select2','required'=>'required')) }}
        </div>
        <div class="form-group col-6">
            {{ Form::label('issue_date', __('Issue Date'),['class'=>'form-control-label']) }}
            {{ Form::text('issue_date',null, array('class' => 'form-control datepicker','required'=>'required')) }}
        </div>
        <div class="form-group col-6">
            {{ Form::label('tax_id', __('Tax %'),['class'=>'form-control-label']) }}
            {{ Form::select('tax_id', $taxes,null, array('class' => 'form-control select2','required'=>'required')) }}
            @if(count($taxes) <= 0)
                <small>
                    {{__('Please create new Tax')}} <a href="{{route('taxes.index')}}">{{__('here')}}</a>.
                </small>
            @endif
        </div>
        <div class="form-group col-12">
            {{ Form::label('terms', __('Terms'),['class'=>'form-control-label']) }}
            {{ Form::textarea('terms',null, array('class' => 'form-control')) }}
        </div>
        <div class="col-12 text-right">
            <input type="submit" value="{{__('Create')}}" class="btn-create badge-blue">
            <input type="button" value="{{__('Cancel')}}" class="btn-create bg-gray" data-dismiss="modal">
        </div>
    </div>
    {{ Form::close() }}
</div>