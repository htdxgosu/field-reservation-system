@extends('layouts.app')
@section('title', 'Tìm kiếm sân')

@section('content')
<!-- Header Start -->
<div class="container-fluid bg-breadcrumb">
    <div class="container text-center custom-header" style="max-width: 900px;">
        <h4 class="text-white display-4 wow fadeInDown" data-wow-delay="0.1s">Tìm kiếm sân</h4>
        <ol class="breadcrumb d-flex justify-content-center mb-0 wow fadeInDown" data-wow-delay="0.3s">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
            <li class="breadcrumb-item active text-primary">Tìm kiếm sân</li>
        </ol>    
    </div>
</div>
<!-- Header End -->

        <!-- Tìm kiếm sân -->
        <div class="container-fluid team py-3">
            <div class="container py-3">
            <a href="/" class="btn btn-secondary mb-3">Về trang chủ</a>
                <h4>Kết quả tìm kiếm sân ngày {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }} </h4>
                @if($fields->isEmpty())
                    <p>Không tìm thấy sân phù hợp với yêu cầu của bạn.</p>
                @else
                <div class="row">
                    @foreach($fields as $field)
                        <div class="col-md-6 mb-4 mt-2">
                            <div class="card d-flex flex-row" style="min-height:440px">
                             <div class="col-md-7 p-2">
                                <img src="{{ $field->image_url ?? 'default-image.jpg' }}" class="img-fluid rounded hover-effect" alt="Hình ảnh sân" 
                                 style="object-fit: cover; width: 100%; min-height: 400px;">
                            </div>
                             <div class="col-md-5">
                                <!-- Nội dung bên phải -->
                                <div class="card-body d-flex flex-column justify-content-between">
                                    <a href="{{ route('fields.show', $field->id) }}">
                                        <h4 class="text-center mb-3 fw-bold">{{ $field->name }}</h4>
                                    </a>
                                        <a href="https://www.google.com/maps?q={{ urlencode($field->location) }}" target="_blank" style="color: inherit;">
                                            <p class="mb-4 small"><strong>Địa chỉ:</strong> {{ $field->location }}</p>
                                        </a>
                                        <p class="mb-2 d-flex justify-content-between">
                                            <strong>Giờ mở cửa:</strong>{{ \Carbon\Carbon::parse($field->opening_time)->format('H:i') }}
                                        </p>
                                        <p class="mb-2 d-flex justify-content-between">
                                            <strong>Giờ đóng cửa:</strong> {{ \Carbon\Carbon::parse($field->closing_time)->format('H:i') }}
                                        </p>
                                        <p class="mb-2 d-flex justify-content-between">
                                            <strong>Giá thường:</strong> <span class="text-danger fw-bold">{{ $field->formatted_price_per_hour }}</span>
                                        </p>
                                        <p class="mb-3 d-flex justify-content-between">
                                            <strong>Giá sau 17h:</strong> <span class="text-danger fw-bold">{{ $field->formatted_peak_price_per_hour }}</span>
                                        </p>
                                        <!-- Nút bấm để hiện thị giờ trống trong modal -->
                                        <button type="button" class="btn btn-info mb-2" data-bs-toggle="modal" data-bs-target="#availableHoursModal{{ $field->id }}">
                                            Xem giờ trống
                                        </button>
                                        <!-- Modal -->
                                        <div class="modal fade" id="availableHoursModal{{ $field->id }}" tabindex="-1" aria-labelledby="availableHoursModalLabel{{ $field->id }}" aria-hidden="true">
                                            <div class="modal-dialog" style="max-width: 350px;">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="availableHoursModalLabel{{ $field->id }}">
                                                        <i class="fas fa-clock me-2"></i> 
                                                            <strong>Giờ trống {{ $field->name }}</strong></h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p class="card-text"><strong>Ngày:</strong> 
                                                        {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}
                                                            @if(!empty($field->availableHours))
                                                                <ul class="list-unstyled px-3">
                                                                    @foreach($field->availableHours as $index => $hour)
                                                                        <li>{{ \Carbon\Carbon::parse($hour['start'])->format('H:i') }} - {{ \Carbon\Carbon::parse($hour['end'])->format('H:i') }}</li>
                                                                    @endforeach
                                                                </ul>
                                                            @else
                                                                <span class="text-danger">Không có giờ trống</span>
                                                            @endif
                                                        </p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <a href="{{ route('fields.show', $field->id) }}" class="btn btn-warning btn-sm w-48">
                                                Chi tiết
                                            </a>
                                    <!-- Nút Đặt sân -->
                                    @auth
                                        <button type="button" class="btn btn-success btn-sm w-48" data-bs-toggle="modal" data-bs-target="#reserveModal{{ $field->id }}">
                                            Đặt sân
                                        </button>
                                    @else
                                        <a href="{{ route('login.login') }}" class="btn btn-secondary btn-sm w-48">
                                            Đăng nhập để đặt sân
                                        </a>
                                    @endauth
                                    </div>
                                    <p class="text-center">Cách vị trí bạn khoảng: {{$field->distance}} km</p>
                                    <div class="text-warning text-end">
                                            <strong>{{ number_format($field->average_rating, 1) }}</strong>
                                            <i class="fas fa-star"></i>
                                    </div>
                                    <!-- Modal Đặt Sân -->
                                    @foreach($fields as $field)
                                     <div class="modal fade" id="reserveModal{{ $field->id }}" tabindex="-1" 
                                        aria-labelledby="reserveModalLabel{{ $field->id }}" aria-hidden="true">
                                        <div class="modal-dialog" style="max-width: 400px;">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="reserveModalLabel{{ $field->id }}">
                                                    <i class="fa fa-futbol m-2"></i><strong>Đặt sân {{ $field->name }}</strong></h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <!-- Form Đặt Sân -->
                                                    <form id="reservationForm{{ $field->id }}" method="POST"
                                                    action="{{ route('confirm-reservation') }}">
                                                        @csrf
                                                        <!-- Hiển thị ngày đã tìm kiếm (Không cho phép thay đổi) -->
                                                        <div class="mb-2">
                                                            <label for="date{{ $field->id }}" class="form-label"><strong>Ngày thuê sân</strong></label>
                                                            <input type="text" class="form-control form-control-sm" id="date{{ $field->id }}" 
                                                            value="{{ \Carbon\Carbon::parse($date)->format('d/m/Y') }} " 
                                                            data-date="{{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}" disabled>
                                                        </div>
                                                        <input type="hidden" name="date" value="{{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}">
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
                                                        <div class="mb-2">
                                                            <label for="note_{{ $field->id }}" class="form-label"><strong>Ghi chú (không bắt buộc)</strong></label>
                                                            <textarea class="form-control" name="note" id="note_{{ $field->id }}" rows="2"></textarea>
                                                        </div>
                                                        <input type="hidden" name="userId" value="{{ Auth::check() ? Auth::user()->id : '' }}">
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
                                </div>
                               </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
@endsection

@push('scripts')  <!-- Nhúng script riêng cho trang này -->
    <script src="{{ asset('js/user/search.js') }}"></script>
@endpush
