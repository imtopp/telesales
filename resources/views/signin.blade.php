@extends('layout\login_template')

@section('title','Login')

@section('content')
@foreach($errors->all() as $error)
    <p class="alert alert-danger">{!!$error!!}</p>
@endforeach
{!!Form::open(['url'=>'/auth/login','id'=>"register_form",'class'=>'form form-horizontal'])!!}
<h1>Login Form</h1>
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
<div>
    {!!Form::submit('Login',['class'=>'btn btn-default submit'])!!}
    {!!HTML::link('/auth/forgetpassword','Lost your password?',['class'=>'reset_pass'])!!}
</div>

<div class="clearfix"></div>

<div class="separator">
  <p class="change_link">New to site?
    {!!HTML::link('/auth/register','Create Account',['class'=>'to_register'])!!}
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
