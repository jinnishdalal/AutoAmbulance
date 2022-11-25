@extends('layouts.admin')
@section('page-title')
    {{__('Manage Expense Category')}}
@endsection

@section('action-button')
    @can('create expense category')
        <div class="all-button-box row d-flex justify-content-end">
            <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 col-6">
                <a href="#" class="btn btn-xs btn-white btn-icon-only width-auto" data-url="{{ route('expensescategory.create') }}" data-ajax-popup="true" data-title="{{__('Create Expense')}}"><i class="fas fa-plus"></i> {{__('Create')}} </a>
            </div>
        </div>
    @endcan
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table class="table table-striped dataTable">
                        <thead>
                        <tr>
                            <th>{{__('Expense Category')}}</th>
                            <th width="250px">{{__('Action')}}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($expenses as $expense)
                            <tr data-id="{{$expense->id}}">
                                <td>
                                    <a>{{$expense->name}}</a>
                                </td>
                                <td class="Action">
                                    <span>
                                    @can('edit expense category')
                                            <a href="#" class="edit-icon" data-url="{{ route('expensescategory.edit',$expense->id) }}" data-ajax-popup="true" data-title="{{__('Edit Expenses')}}" class="table-action" ><i class="fas fa-pencil-alt"></i></a>
                                        @endcan
                                        @can('delete expense category')
                                            <a href="#" class="delete-icon"  data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$expense->id}}').submit();"><i class="fas fa-trash"></i></a>
                                            {!! Form::open(['method' => 'DELETE', 'route' => ['expensescategory.destroy', $expense->id],'id'=>'delete-form-'.$expense->id]) !!}
                                            {!! Form::close() !!}
                                        @endcan
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
