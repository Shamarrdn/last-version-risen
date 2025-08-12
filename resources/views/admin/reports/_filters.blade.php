<div class="mb-8 bg-white shadow-sm rounded-lg overflow-hidden">
    <div class="p-6">
        <form action="{{ route('admin.reports.index') }}" method="GET">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- اختيار نوع الفترة -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">نوع الفترة</label>
                    <select name="period" id="period-select"
                            class="block w-full px-3 py-2 text-gray-700 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <option value="today" @selected(request('period') === 'today')>اليوم</option>
                        <option value="week" @selected(request('period') === 'week')>هذا الأسبوع</option>
                        <option value="month" @selected(request('period') === 'month' || !request('period'))>هذا الشهر</option>
                        <option value="year" @selected(request('period') === 'year')>هذه السنة</option>
                    </select>
                </div>

                <!-- حقول التاريخ تظهر دائماً -->
                <div class="md:col-span-2 grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">من تاريخ</label>
                        <input type="date" name="start_date" value="{{ request('start_date') }}"
                               class="block w-full px-3 py-2 text-gray-700 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">إلى تاريخ</label>
                        <input type="date" name="end_date" value="{{ request('end_date') }}"
                               class="block w-full px-3 py-2 text-gray-700 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400">
                    </div>
                </div>

                <!-- أزرار التحكم -->
                <div class="flex items-end space-x-2 rtl:space-x-reverse">
                    <button type="submit" class="btn btn-primary">
                        تطبيق الفلتر
                    </button>
                    <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary">
                        إلغاء الفلتر
                    </a>
                    <button type="button" class="btn btn-success" onclick="window.print()">
                        <i class="fas fa-print me-1"></i> طباعة
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const periodSelect = document.getElementById('period-select');
    const customDateInputs = document.querySelector('.custom-date-inputs');

    function toggleCustomDateInputs() {
        customDateInputs.style.display = periodSelect.value === 'custom' ? 'grid' : 'none';
    }

    periodSelect.addEventListener('change', toggleCustomDateInputs);

    // تشغيل الدالة عند تحميل الصفحة
    toggleCustomDateInputs();
});
</script>
@endpush
