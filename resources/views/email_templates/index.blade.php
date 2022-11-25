@extends('layouts.admin')

@section('page-title')
    {{__('Manage Email Templates')}}
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table class="table table-striped dataTable">
                        <thead>
                        <tr>
                            <th width="92%">{{__('Name')}}</th>
                            <th>{{__('Action')}}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($EmailTemplates as $EmailTemplate)
                            <tr>
                                <td>{{ $EmailTemplate->name }}</td>
                                <td class="Action">
                    <span>
                    @can('edit email template lang')
                            <a href="{{ route('manage.email.language',[$EmailTemplate->id,\Auth::user()->currentLanguage()]) }}" class="edit-icon">
                            <i class="fas fa-eye"></i>
                        </a>
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
