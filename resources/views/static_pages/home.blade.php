@extends('layouts.default')
@section('content')
    @if (Auth::check())
        <div class="row">
            <div class="col-md-8">
                <section class="status_form">
                    @include('shared._status_form')
                </section>
                <h3>微博列表</h3>
                @include ('shared._feed')
            </div>
            <aside class="col-md-4">
                <section class="user_info">
                    @include ('shared._user_info',['user'=>Auth::user()])
                </section>

                <section class="stats">
                    @include ('shared._stats',['user'=>Auth::user()])
                </section>
            </aside>
        </div>
    @else
    <div class="jumbotron">
        <h1>Welcome to MicroBlog</h1>
        <p class="lead">
            你现在所看到的是<a href="http://www.yangp67.com">微博项目</a>主页
        </p>
        <p>
            一切，又将重新启动！
        </p>
        <p>
            <a href="{{ route('signup') }}" class="btn btn-lg btn-success" role="button">现在注册</a>
        </p>
    </div>
    @endif
@stop