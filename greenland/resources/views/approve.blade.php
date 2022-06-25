{{-- @php
    $edit = !is_null($dataTypeContent->getKey());
    $add  = is_null($dataTypeContent->getKey());
@endphp --}}

@extends('voyager::master')

@section('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .panel .mce-panel {
            border-left-color: #fff;
            border-right-color: #fff;
        }

        .panel .mce-toolbar,
        .panel .mce-statusbar {
            padding-left: 20px;
        }

        .panel .mce-edit-area,
        .panel .mce-edit-area iframe,
        .panel .mce-edit-area iframe html {
            padding: 0 10px;
            min-height: 350px;
        }

        .mce-content-body {
            color: #555;
            font-size: 14px;
        }

        .panel.is-fullscreen .mce-statusbar {
            position: absolute;
            bottom: 0;
            width: 100%;
            z-index: 200000;
        }

        .panel.is-fullscreen .mce-tinymce {
            height:100%;
        }

        .panel.is-fullscreen .mce-edit-area,
        .panel.is-fullscreen .mce-edit-area iframe,
        .panel.is-fullscreen .mce-edit-area iframe html {
            height: 100%;
            position: absolute;
            width: 99%;
            overflow-y: scroll;
            overflow-x: hidden;
            min-height: 100%;
        }
    </style>
@stop

@section('content')
<div class="container">
    @if (session('message'))
        <div class="alert alert-success" role="alert">
            {{session('message')}}
        </div>
    @endif
</div>
<div class="container"  >
    <div class="row">
        <div class="col-lg-8 " style="margin-left:20%">
            <table class="table ">
                <th>User name</th>
                <th>Service name</th>
                <th>Status</th>
                @foreach ($users as $user)
                    <tr>
                        <td>{{$user->name}}</td>
                        <td>{{$service[$user->services]->title}}</td>
                        <td><form action="/admin/approve/done" method="post" class="form-group" style="margin-top:5%"> 
                            @csrf
                            <div class="col-4">
                            <input type="hidden" name="user_id" class="form-control" value='{{$user->id}}'>
                            </div>
                            <div class="col-4">
                            <input type="hidden" name="service_id" class="form-control" value='{{$user->services}}'>
                            </div>
                            <button type="submit" class="btn btn-success ">Approve</button>
                        </form></td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
</div>
@stop