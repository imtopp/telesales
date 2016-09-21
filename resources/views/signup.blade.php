@extends('layout\login_template')

@section('title','Pendaftaran')

@section('content')
@foreach($errors->all() as $error)
    <p class="alert alert-danger">{!!$error!!}</p>
@endforeach
{!!Form::open(['url'=>'/auth/register','id'=>"register_form",'class'=>'form form-horizontal'])!!}
<h1>Create Account</h1>
<div class="form-group">
    <div class="controls">
        {!! Form::email('email',Input::old('email'),['class'=>'form-control','placeholder'=>'Email','required']) !!}
    </div>
</div>
<div class="form-group">
    <div class="controls">
        {!! Form::password('password',['class'=>'form-control','placeholder'=>'Password','required']) !!}
    </div>
</div>
<div class="form-group">
    <div class="controls">
        {!! Form::password('password_confirmation',['class'=>'form-control','placeholder'=>'Confirm Password','required']) !!}
    </div>
</div>
<div>
    {!!Form::submit('Register',['class'=>'btn btn-default submit','style'=>'float: initial; margin-left: initial;'])!!}
</div>

<div class="clearfix"></div>

<div class="separator">
  <p class="change_link">Already a member ?
    {!!HTML::link('/auth/login','Log In',['class'=>'to_register'])!!}
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
