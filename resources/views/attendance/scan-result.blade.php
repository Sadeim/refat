<!doctype html>
<html lang="ar" dir="rtl">
<head>
<meta charset="utf-8">
<title>تسجيل الحضور — {{ $employee->name_ar }}</title>
<style>
    body { font-family: 'Tahoma', sans-serif; background: linear-gradient(135deg,#0f3a8a,#2d7be5); min-height: 100vh; margin:0; display:flex; align-items:center; justify-content:center; padding: 20px; }
    .card { background:#fff; border-radius: 18px; padding: 40px; max-width: 480px; width:100%; text-align: center; box-shadow: 0 30px 60px rgba(0,0,0,.25); }
    .icon { width: 80px; height: 80px; border-radius: 50%; margin: 0 auto 20px; display:flex; align-items:center; justify-content:center; font-size: 40px; }
    .ok { background: #ecfdf5; color: #047857; }
    .out { background: #eff6ff; color: #1d4ed8; }
    .warn { background: #fef3c7; color: #b45309; }
    h1 { margin: 8px 0; font-size: 22px; color:#0f3a8a; }
    .name { font-size: 24px; font-weight: 700; margin: 5px 0; }
    .code { color:#6b7280; font-size: 13px; }
    .row { background:#f9fafb; padding: 12px; border-radius: 10px; margin-top:14px; display:flex; justify-content:space-between; }
    .row b { color:#374151; }
    .time { font-size: 28px; font-weight: 700; color: #0f3a8a; margin: 12px 0; }
</style>
</head>
<body>
<div class="card">
    @if ($action === 'check_in')
        <div class="icon ok">✓</div>
        <h1>تم تسجيل الدخول</h1>
    @elseif ($action === 'check_out')
        <div class="icon out">←</div>
        <h1>تم تسجيل الخروج</h1>
    @else
        <div class="icon warn">!</div>
        <h1>سُجِّل اليوم بالفعل</h1>
    @endif

    <div class="name">{{ $employee->name_ar }}</div>
    <div class="code">{{ $employee->code }} — {{ $employee->position }}</div>
    <div class="time">{{ now()->format('H:i') }}</div>

    @if ($attendance->check_in)<div class="row"><b>الدخول</b><span>{{ \Carbon\Carbon::parse($attendance->check_in)->format('H:i') }}</span></div>@endif
    @if ($attendance->check_out)<div class="row"><b>الخروج</b><span>{{ \Carbon\Carbon::parse($attendance->check_out)->format('H:i') }}</span></div>@endif
    @if ($attendance->hours)<div class="row"><b>عدد الساعات</b><span>{{ number_format($attendance->hours, 2) }}</span></div>@endif
</div>
</body>
</html>
