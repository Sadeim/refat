@php
    use App\Models\Attendance;
@endphp

<div style="display:flex; flex-direction:column; gap:14px;">
    {{-- ملخّص الشهر --}}
    <div style="display:grid; grid-template-columns: repeat(4, 1fr); gap:10px;">
        <div style="background:#eff6ff; border-radius:10px; padding:10px;">
            <div style="font-size:11px; color:#6b7280;">إجمالي الساعات</div>
            <div style="font-size:16px; font-weight:700;">{{ number_format($wage->total_hours, 2) }} س</div>
        </div>
        <div style="background:#ecfdf5; border-radius:10px; padding:10px;">
            <div style="font-size:11px; color:#6b7280;">إجمالي الأجر</div>
            <div style="font-size:16px; font-weight:700; color:#047857;">{{ number_format($wage->total_amount, 2) }} ₪</div>
        </div>
        <div style="background:#f9fafb; border-radius:10px; padding:10px;">
            <div style="font-size:11px; color:#6b7280;">أيام الدوام</div>
            <div style="font-size:16px; font-weight:700;">{{ $wage->work_days }} / غياب {{ $wage->absence_days }} / إجازة {{ $wage->leave_days }}</div>
        </div>
        <div style="background:{{ $wage->paid_at ? '#ecfdf5' : '#fef2f2' }}; border-radius:10px; padding:10px;">
            <div style="font-size:11px; color:#6b7280;">الحالة</div>
            <div style="font-size:14px; font-weight:700; color:{{ $wage->paid_at ? '#047857' : '#b91c1c' }};">
                {{ $wage->paid_at ? 'مدفوع — '.$wage->paid_at->format('Y-m-d') : 'غير مدفوع' }}
            </div>
            @if ($wage->transaction)
                <div style="font-size:11px; color:#6b7280; margin-top:4px;">قيد: {{ $wage->transaction->reference_no }}</div>
            @endif
        </div>
    </div>

    {{-- جدول الحضور التفصيلي --}}
    <div style="border:1px solid #e5e7eb; border-radius:10px; overflow:hidden;">
        <table style="width:100%; border-collapse:collapse; font-size:13px;">
            <thead style="background:#f9fafb;">
                <tr>
                    <th style="padding:8px; text-align:start;">التاريخ</th>
                    <th style="padding:8px; text-align:start;">اليوم</th>
                    <th style="padding:8px; text-align:start;">الفترة</th>
                    <th style="padding:8px; text-align:start;">من</th>
                    <th style="padding:8px; text-align:start;">إلى</th>
                    <th style="padding:8px; text-align:start;">ساعات</th>
                    <th style="padding:8px; text-align:start;">سعر</th>
                    <th style="padding:8px; text-align:start;">الإجمالي</th>
                    <th style="padding:8px; text-align:start;">الحالة</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($attendances as $a)
                    @php
                        $statusColors = ['present'=>'#16a34a','absent'=>'#dc2626','leave'=>'#f59e0b','late'=>'#eab308','half_day'=>'#6b7280'];
                        $color = $statusColors[$a->status] ?? '#374151';
                    @endphp
                    <tr style="border-top:1px solid #e5e7eb;">
                        <td style="padding:8px;">{{ $a->date?->format('Y-m-d') }}</td>
                        <td style="padding:8px;">{{ $a->date?->translatedFormat('l') }}</td>
                        <td style="padding:8px;">{{ Attendance::PERIODS[$a->period] ?? '—' }}</td>
                        <td style="padding:8px;">{{ $a->check_in?->format('H:i') ?? '—' }}</td>
                        <td style="padding:8px;">{{ $a->check_out?->format('H:i') ?? '—' }}</td>
                        <td style="padding:8px; font-weight:600;">{{ number_format($a->hours, 2) }}</td>
                        <td style="padding:8px;">{{ number_format($a->hourly_rate, 2) }} ₪</td>
                        <td style="padding:8px; font-weight:700; color:#047857;">{{ number_format($a->daily_total, 2) }} ₪</td>
                        <td style="padding:8px; color:{{ $color }}; font-weight:600;">{{ Attendance::STATUSES[$a->status] ?? $a->status }}</td>
                    </tr>
                @endforeach
                @if ($attendances->isEmpty())
                    <tr><td colspan="9" style="padding:14px; text-align:center; color:#6b7280;">لا توجد سجلات حضور لهذا الشهر</td></tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
