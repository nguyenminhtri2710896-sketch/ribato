@extends('backend.v2.layouts.tool')
@section('title', __('Xác nhận yêu cầu rút tiền'))
@section('style')
@endsection
@section('javascript')
    <script src="{{ asset('backend/v2/customes/js/tool.js') }}?v={{ config('app.asset_version') }}"></script>
    <script type="text/javascript">
        tool.index();
    </script>
@endsection
@section('content')
    <div class="account-pages my-5 pt-sm-5">
        <div class="container">

        </div>
    </div>
@endsection