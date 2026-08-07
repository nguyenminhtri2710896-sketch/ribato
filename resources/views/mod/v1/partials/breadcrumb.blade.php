<div class="me-auto">
    <h4 class="page-title">{{ $title }}</h4>
    <div class="d-inline-block align-items-center">
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#"><i class="mdi mdi-home-outline"></i></a></li>
                @foreach ($list as $num => $item)
                    @if (empty($item['url']))
                        <li class="breadcrumb-item {{ count($list) == $num + 1 ? 'active' : '' }}" aria-current="page">
                            {{ $item['title'] }}
                        </li>
                    @else
                        <li class="breadcrumb-item {{ count($list) == $num + 1 ? 'active' : '' }}" aria-current="page"><a
                                href="{{ $item['url'] }}">{{ $item['title'] }}</a></li>
                    @endif
                @endforeach
                </li>
            </ol>
        </nav>
    </div>
</div>
