@extends('admin.layouts.dashboard')
@section('title', 'Lịch thi đấu')

@section('content')
<div class="container">
    <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">Trang chủ</a></li>
                <li class="breadcrumb-item active" aria-current="page">Lịch thi đấu các sân</li>
            </ol>
    </nav>
    <div class="col-md-3 mb-4">
        <form method="GET" action="{{ route('admin.reservations.indexTable') }}" class="d-flex">
            <div class="form-group flex-grow-1 me-2">
                <input type="text" id="locDate" name="date" class="form-control" placeholder="Chọn ngày" value="{{ request('date') }}">
            </div>
            <a href="{{ route('admin.reservations.indexTable') }}" class="btn btn-secondary mx-2">Reset</a>
            <button type="submit" class="btn btn-primary">Lọc</button>
        </form>
    </div>
    <h3 class="mb-4">
        <i class="fas fa-calendar-alt"></i> Lịch thi đấu các sân ngày {{$dateFormatted}}
    </h3>
    <div class="row">
        @foreach($schedules as $fieldName => $schedule)
            <div class="col-md-4 mb-4">
                <div class="card shadow-lg border-light">
                    <div class="card-header bg-primary text-white text-center">
                        <h5>{{ $fieldName }}</h5>
                    </div>
                    <div class="card-body">
                        @if(is_string($schedule))
                         <p class="text-center">
                            {!! str_replace('Đang trống', '<span class="status-available">Đang trống</span>', $schedule) !!}
                        </p>
                        @else
                            <ul class="list-unstyled text-center">
                                @foreach($schedule as $item)
                                <li class="py-3">
                                    @if($item['status'] === 'Đã được đặt')
                                        <a href="{{ route('admin.reservations.show',  ['reservation' => $item['reservation_id']]) }}">
                                        Từ {{ $item['start'] }} đến {{ $item['end'] }}: <span class="status-booked">{{ $item['status'] }}</span>
                                        </a>
                                    @else
                                         Từ {{ $item['start'] }} đến {{ $item['end'] }}: <span class="status-available">{{ $item['status'] }}</span>
                                    @endif
                                </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
 </div>
@endsection


