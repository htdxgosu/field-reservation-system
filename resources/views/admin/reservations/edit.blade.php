@extends('admin.layouts.dashboard')
@section('title', 'Chỉnh sửa đơn đặt sân')

@section('content')
<div class="container">
    <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.reservations.index') }}">Quản lý đặt sân</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.reservations.show', $reservation->id) }}">Chi tiết đơn</a></li>
                <li class="breadcrumb-item active" aria-current="page">Chỉnh sửa</li>
            </ol>
    </nav>
    <h4><i class="fas fa-edit mb-4"></i> Chỉnh sửa đơn đặt sân</h4>
    <div class="col-md-6">
        <form id="updateReservationForm" action="{{ route('admin.reservations.update', $reservation->id) }}" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" id="reservation_id" value="{{ $reservation->id }}">
            <!-- Chọn sân -->
            <div class="form-group mb-2">
                <label for="field_id"><strong>Chọn sân</strong> <span class="text-danger">*</span></label>
                <select name="field_id" id="field_id" class="form-control" required>
                    @foreach($fields as $field)
                        <option value="{{ $field->id }}" {{ old('field_id', $reservation->field_id) == $field->id ? 'selected' : '' }}>
                            {{ $field->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <!-- Ghi chú -->
            <div class="form-group mb-2">    
                <label for="note"><strong>Ghi chú</strong></label>
                <textarea id="note" name="note" class="form-control">{{ old('note', $reservation->note) }}</textarea>
            </div>
            <!-- Ngày-->
            <div class="form-group mb-2">
                <label for="date"><strong>Chọn ngày</strong> <span class="text-danger">*</span></label>
                <input type="text" id="date" name="date" value="{{ old('date', $reservation->start_time->format('d/m/Y')) }}" class="form-control"
                required>
            
            </div>
            <div class="mt-2">
            <button type="button" class="btn btn-info" onclick="checkAvailability(event)">Kiểm tra giờ trống</button>
            </div>
            <div class="mt-2">
            <span id="availableHoursContainer" style="display: none;">
                <ul class="available-hours-list" id="availableHoursList">
                </ul>
                <span class="text-danger" id="noAvailableHoursMessage" style="display: none;">Không có giờ trống</span>
            </span>
            </div>

            <!-- Giờ vào -->
            <div class="form-group mb-2">
            <label class="form-label"><strong>Giờ bắt đầu</strong> <span class="text-danger">*</span></label>
                <select 
                    class="form-select" 
                    id="start_time" 
                    name="start_time" 
                    aria-label="Giờ bắt đầu" 
                    required>
                    @foreach($availableStartTimes as $startTime)
                        <option 
                            value="{{ $startTime }}" 
                            {{ old('start_time', $reservation->start_time->format('H:i')) == $startTime ? 'selected' : '' }}>
                            {{ $startTime }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Thời gian đá -->
            <div class="form-group mb-2">
                <label for="duration"><strong>Thời gian đá</strong> <span class="text-danger">*</span></label>
                <select id="duration" name="duration" class="form-control" required>
                    @foreach ($durations as $duration)
                        <option value="{{ $duration->duration }}" 
                            {{ old('duration', $reservation->duration_id) == $duration->id ? 'selected' : '' }}>
                            {{ $duration->duration }} phút
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-primary mt-2">Lưu thay đổi</button>
        </form>
    </div>
    
</div>
@endsection
@push('scripts')
    
        @if(session('swal-type') && session('swal-message'))
        <script>
            Swal.fire({
                icon: "{{ session('swal-type') }}",           
                title: "{{ session('swal-message') }}",       
                showConfirmButton: true,      
                customClass: {
        title: 'swal-title'  // Gán lớp CSS cho tiêu đề
    }                                        
            });
            </script>
        @endif

        @if ($errors->any())
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Đã xảy ra lỗi',
                html: `{!! implode('<br>', $errors->all()) !!}`, 
                showConfirmButton: true,
                customClass: {
        title: 'swal-title'  // Gán lớp CSS cho tiêu đề
    }
            });
            </script>
        @endif
   
@endpush
@push('scripts')  
    <script src="{{ asset('js/edit-reservation.js') }}"></script>
@endpush