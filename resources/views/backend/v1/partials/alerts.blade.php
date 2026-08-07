@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show mb-0" role="alert">
        @foreach ($errors->all() as $error)
            <p>- {{ $error }}<br /></p>
        @endforeach
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-0" role="alert">
        {!! session('error') !!}
    </div>
@endif

@if (session('success'))
    <div class="alert alert-primary alert-dismissible fade show" role="alert">
        {!! session('success') !!}
    </div>
@endif
