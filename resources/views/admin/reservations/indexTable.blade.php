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
    <h3 class="mb-2">
        <i class="fas fa-calendar-alt"></i> Lịch thi đấu các sân ngày {{$dateFormatted}}
    </h3>
    <div class="mb-4">
        <a href="#" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#chooseFieldModal">
            Tạo đơn đặt sân
        </a>
    </div>
        <!-- Modal chọn sân -->
    <div class="modal fade" id="chooseFieldModal" tabindex="-1" aria-labelledby="chooseFieldModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="max-width:400px">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="chooseFieldModalLabel">Chọn sân</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul class="list-group">
                        @foreach($fields as $field)
                         <a href="" data-bs-toggle="modal" data-bs-target="#reserveModal{{ $field->id }}">
                            <li class="list-group-item">
                                    {{ $field->name }}
                            </li>
                         </a>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
    @foreach($fields as $field)
    <!-- Modal đặt sân cho từng sân -->
        <div class="modal fade" id="reserveModal{{ $field->id }}" tabindex="-1" aria-labelledby="reserveModalLabel{{ $field->id }}" aria-hidden="true">
            <div class="modal-dialog" style="max-width:400px">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="reserveModalLabel{{ $field->id }}">Đặt {{ $field->name }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                     <form id="bookingForm{{ $field->id }}" method="POST"
                        action="{{ route('confirm-reservation-admin') }}">
                            @csrf
                            <!-- Hiển thị ngày đã tìm kiếm (Không cho phép thay đổi) -->
                            <div class="mb-2">
                                <label for="date{{ $field->id }}" class="form-label"><strong>Ngày thuê sân</strong></label>
                                <input type="text" class="form-control form-control-sm" id="date{{ $field->id }}" 
                                value="{{$dateFormatted}}" 
                                data-date="{{$dateFormatted}}" disabled>
                            </div>
                            <input type="hidden" name="date" value="{{$dateFormatted}}">
                            <div class="mb-2">
                                <label class="form-label"><strong>Giờ bắt đầu</strong></label>
                                <select class="form-select" 
                                id="start_time_{{ $field->id }}" 
                                name="start_time"
                                aria-label="Giờ bắt đầu"  required>
                                <option value="">Chọn giờ bắt đầu</option>
                                    @foreach($field->availableStartTimes as $startTime)
                                        <option value="{{ $startTime }}">{{ $startTime }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-2">
                                <label class="form-label"><strong>Thời gian đá</strong></label>
                                <select class="form-select" 
                                name="duration" 
                                id="duration_{{ $field->id }}" required>
                                <option value="">Chọn thời gian đá</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="phone_{{ $field->id }}" class="form-label"><strong>Số điện thoại</strong>
                                </label>
                                <input type="tel" 
                                    class="form-control" 
                                    id="phone_{{ $field->id }}" 
                                    name="phone" 
                                    required>
                                    <div id="phoneError_{{ $field->id }}" class="text-danger mt-2" style="display:none;">
                                        Số điện thoại không hợp lệ.
                                    </div>
                            </div>
                            <div class="mb-3">
                                <label for="note_{{ $field->id }}" class="form-label"><strong>Ghi chú (không bắt buộc)</strong></label>
                                <textarea class="form-control" name="note" id="note_{{ $field->id }}" rows="2"></textarea>
                            </div>

                            <input type="hidden" name="field_id" value="{{ $field->id }}">
                            <div class="d-flex justify-content-center mt-4">
                                <button type="button" class="btn btn-primary mx-2" data-bs-dismiss="modal">Hủy</button>
                                <button type="button" class="btn btn-success mx-2 continue-btn">
                                    Tiếp tục
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

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
@push('scripts')
<script src="{{ asset('js/admin/create-reservation.js') }}"></script>
@endpush

