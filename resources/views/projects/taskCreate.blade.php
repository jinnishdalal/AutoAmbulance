<div class="card bg-none card-box">
    {{ Form::open(array('route' => array('task.store',$project->id))) }}
    <div class="row">
        <div class="form-group  col-md-6">
            {{ Form::label('title', __('Title'),['class'=>'form-control-label']) }}
            {{ Form::text('title', '', array('class' => 'form-control','required'=>'required')) }}
        </div>
        <div class="form-group  col-md-6">
            {{ Form::label('priority', __('Priority'),['class'=>'form-control-label']) }}
            {!! Form::select('priority', $priority, null,array('class' => 'form-control select2','required'=>'required')) !!}
        </div>
        <div class="form-group  col-md-6">
            {{ Form::label('start_date', __('Start Date'),['class'=>'form-control-label']) }}
            {{ Form::text('start_date', '', array('class' => 'form-control datepicker','required'=>'required')) }}
        </div>
        <div class="form-group  col-md-6">
            {{ Form::label('due_date', __('Due Date'),['class'=>'form-control-label']) }}
            {{ Form::text('due_date', '', array('class' => 'form-control datepicker','required'=>'required')) }}
        </div>
        @if(\Auth::user()->type == 'company')
            <div class="form-group  col-md-6">
                {{ Form::label('assign_to', __('Assign To'),['class'=>'form-control-label']) }}
                {!! Form::select('assign_to', $users, null,array('class' => 'form-control select2','required'=>'required')) !!}
            </div>
        @endif
        <div class="form-group  col-md-6">
            {{ Form::label('milestone_id', __('Milestone'),['class'=>'form-control-label']) }}
            {!! Form::select('milestone_id', $milestones, null,array('class' => 'form-control select2')) !!}
        </div>
    </div>
    <div class="row">
        <div class="form-group  col-md-12">
            {{ Form::label('description', __('Description'),['class'=>'form-control-label']) }}
            {!! Form::textarea('description', null, ['class'=>'form-control ','rows'=>'2']) !!}
        </div>

        <div class="col-12 text-right">
            <input type="submit" value="{{__('Create')}}" class="btn-create badge-blue">
            <input type="button" value="{{__('Cancel')}}" class="btn-create bg-gray" data-dismiss="modal">
        </div>
    </div>
    {{ Form::close() }}
</div>
