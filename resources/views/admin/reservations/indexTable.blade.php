@extends('admin.layouts.dashboard')
@section('title', 'Quản lý đặt sân')

@section('content')
<div class="container">
    <h3 class="mb-4">Quản lý đặt sân - Dạng lịch</h3>
    
    <!-- Dropdown chọn sân -->
    <div class="mb-3">
        <label for="fieldSelect" class="form-label">Chọn sân</label>
        <select id="fieldSelect" class="form-select" onchange="loadFieldSchedule(this.value)">
            <option value="">Chọn sân</option>
            <option value="1">Sân 1</option>
            <option value="2">Sân 2</option>
            <option value="3">Sân 3</option>
        </select>
    </div>

    <!-- Lịch của sân -->
    <div id="calendar"></div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        plugins: ['timeGrid'],
        initialView: 'timeGridWeek',
        locale: 'vi',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'timeGridDay,timeGridWeek'
        },
        events: function(info, successCallback, failureCallback) {
            var events = [
                {
                    title: 'Đặt sân A',
                    start: '2024-12-12T09:00:00',
                    end: '2024-12-12T11:00:00',
                    description: 'Đặt sân 1'
                },
                {
                    title: 'Đặt sân B',
                    start: '2024-12-12T13:00:00',
                    end: '2024-12-12T15:00:00',
                    description: 'Đặt sân 2'
                }
            ];
            successCallback(events);
        }
    });

    calendar.render();
});
</script>
@endpush
