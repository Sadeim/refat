<!doctype html>
<html lang="ar" dir="rtl">
<head>
<meta charset="utf-8">
<title>بطاقة موظف — {{ $employee->name_ar }}</title>
<style>
    * { box-sizing: border-box; }
    body {
        font-family: 'Tahoma', 'Segoe UI', sans-serif;
        background: #eef2f7;
        margin: 0;
        padding: 30px;
    }
    .card {
        width: 86mm;
        height: 54mm;
        background: linear-gradient(135deg, #0f3a8a 0%, #1e5fc1 60%, #2d7be5 100%);
        color: #fff;
        border-radius: 14px;
        box-shadow: 0 18px 35px rgba(15,58,138,.35);
        padding: 14px 16px;
        display: grid;
        grid-template-columns: 1fr 90px;
        gap: 10px;
        position: relative;
        overflow: hidden;
        margin: 0 auto 20px;
    }
    .card::before {
        content: '';
        position: absolute;
        inset: -40px -40px auto auto;
        width: 160px; height: 160px;
        background: rgba(255,255,255,.08);
        border-radius: 50%;
    }
    .brand {
        font-size: 14px;
        font-weight: 700;
        letter-spacing: .5px;
        opacity: .9;
    }
    .brand small { display: block; font-size: 9px; opacity: .8; font-weight: 400; }
    .info { margin-top: 6px; }
    .info .name {
        font-size: 16px;
        font-weight: 700;
        margin: 4px 0;
    }
    .info .position { font-size: 11px; opacity: .85; }
    .info .meta { font-size: 10px; opacity: .8; margin-top: 6px; line-height: 1.6; }
    .right {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: space-between;
    }
    .photo {
        width: 64px; height: 64px;
        border-radius: 50%;
        background: #fff;
        background-size: cover;
        background-position: center;
        border: 3px solid rgba(255,255,255,.6);
    }
    .qr {
        width: 60px; height: 60px;
        background: #fff;
        padding: 3px;
        border-radius: 4px;
    }
    .qr img { width: 100%; height: 100%; }
    .actions { text-align:center; margin-top: 12px; }
    .actions button {
        background: #1e5fc1; color: #fff; border: none;
        padding: 8px 18px; border-radius: 8px; cursor: pointer;
        font-family: inherit;
    }
    @media print {
        body { background: #fff; padding: 0; }
        .actions { display: none; }
    }
</style>
</head>
<body>

<div class="card">
    <div>
        <div class="brand">Trust Guard <small>ترست جارد للحماية واللوجستيات</small></div>
        <div class="info">
            <div class="name">{{ $employee->name_ar }}</div>
            <div class="position">{{ $employee->position ?? '' }}</div>
            <div class="meta">
                الكود: {{ $employee->code }}<br>
                @if($employee->phone) الهاتف: {{ $employee->phone }}<br>@endif
                @if($employee->start_date) المباشرة: {{ $employee->start_date->format('Y-m-d') }} @endif
            </div>
        </div>
    </div>
    <div class="right">
        <div class="photo" @if($employee->photo_url) style="background-image:url('{{ $employee->photo_url }}')" @endif></div>
        <div class="qr"><img src="data:image/svg+xml;base64,{{ $qr }}" alt="QR"></div>
    </div>
</div>

<div class="actions">
    <button onclick="window.print()">طباعة البطاقة</button>
</div>

</body>
</html>
