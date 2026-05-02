<!doctype html>
<html lang="ar" dir="rtl">
<head>
<meta charset="utf-8">
<title>التحقق من الموظف — {{ $employee->name_ar }}</title>
<style>
    body { font-family: 'Tahoma', sans-serif; background: #f4f6fb; margin: 0; padding: 40px; }
    .wrap { max-width: 480px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 14px; box-shadow: 0 12px 30px rgba(0,0,0,.08); text-align: center; }
    .photo { width: 110px; height: 110px; border-radius: 50%; margin: 0 auto 15px; background: #ddd; background-size: cover; background-position: center; }
    h1 { margin: 8px 0; font-size: 22px; color: #0f3a8a; }
    .badge { display: inline-block; background: #e8f0fe; color: #0f3a8a; padding: 4px 12px; border-radius: 99px; font-size: 12px; margin: 4px; }
    .row { text-align: right; padding: 8px 0; border-bottom: 1px solid #eee; }
    .row span { color: #666; font-size: 13px; }
    .verified { color: #0a7d2c; font-weight: 700; margin-top: 10px; }
</style>
</head>
<body>
<div class="wrap">
    <div class="photo" @if($employee->photo_url) style="background-image:url('{{ $employee->photo_url }}')" @endif></div>
    <h1>{{ $employee->name_ar }}</h1>
    <div>
        <span class="badge">{{ $employee->code }}</span>
        @if($employee->position)<span class="badge">{{ $employee->position }}</span>@endif
        @if($employee->department)<span class="badge">{{ $employee->department }}</span>@endif
    </div>
    @if($employee->phone)<div class="row"><span>الهاتف:</span> {{ $employee->phone }}</div>@endif
    @if($employee->start_date)<div class="row"><span>تاريخ المباشرة:</span> {{ $employee->start_date->format('Y-m-d') }}</div>@endif
    <div class="row"><span>الحالة:</span> {{ ['active'=>'نشط','on_leave'=>'في إجازة','suspended'=>'موقوف','terminated'=>'منتهي'][$employee->status] ?? $employee->status }}</div>
    <div class="verified">✓ تم التحقق — Trust Guard</div>
</div>
</body>
</html>
