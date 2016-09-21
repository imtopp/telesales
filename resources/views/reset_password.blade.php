@extends('layout\login_template')

@section('title','Reset Password')

@section('content')
@foreach($errors->all() as $error)
    <p class="alert alert-danger">{!!$error!!}</p>
@endforeach
{!!Form::open(['url'=>'/auth/forgot_password','id'=>"forgot_password_form",'class'=>'form form-horizontal'])!!}
<h1>Reset Password Form</h1>
<div class="form-group">
    <div class="controls">
        {!! Form::email('email',Input::old('email'),['class'=>'form-control','placeholder'=>'Email','required']) !!}
    </div>
</div>
<div>
    {!!Form::submit('Send Reset Password Link',['class'=>'btn btn-default submit','style'=>'float: initial; margin-left: initial;'])!!}
</div>

<div class="clearfix"></div>

<div class="separator">
  <p class="change_link">Already remember ?
    <a href="/auth/login" class="to_register"> Log in </a>
  </p>

  <div class="clearfix"></div>
  <br />

  <div>
    <h1><i class="fa {{ config('settings.fa_icon') }}"></i> {{ config('settings.app_name') }}</h1>
    <p>©2016 All Rights Reserved. Gentelella Alela! is a Bootstrap 3 template. Privacy and Terms</p>
  </div>
</div>
{!!Form::close()!!}
@endsection
