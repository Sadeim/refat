<?php

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Transaction;
use Illuminate\Support\Facades\Route;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

Route::get('/', function () {
    return redirect('/admin');
});

Route::get('/employees/{employee}/card', function (Employee $employee) {
    return view('employees.card', [
        'employee' => $employee,
        'qr' => base64_encode(QrCode::format('svg')->size(200)->generate(route('employees.verify', $employee->qr_token))),
    ]);
})->name('employees.card');

Route::get('/employees/verify/{token}', function (string $token) {
    $employee = Employee::where('qr_token', $token)->firstOrFail();
    return view('employees.verify', ['employee' => $employee]);
})->name('employees.verify');

Route::get('/attendance/scan/{token}', function (string $token) {
    $employee = Employee::where('qr_token', $token)->firstOrFail();
    $now = now();
    $attendance = Attendance::firstOrNew([
        'employee_id' => $employee->id,
        'date' => $now->toDateString(),
    ]);

    $action = 'check_in';
    if (! $attendance->check_in) {
        $attendance->check_in = $now->format('H:i:s');
        $attendance->status = $employee->shift_start && $now->format('H:i') > $employee->shift_start ? 'late' : 'present';
        $attendance->check_in_method = 'qr';
    } elseif (! $attendance->check_out) {
        $attendance->check_out = $now->format('H:i:s');
        $action = 'check_out';
        if ($attendance->check_in) {
            $in = \Carbon\Carbon::parse($attendance->check_in);
            $out = \Carbon\Carbon::parse($attendance->check_out);
            $attendance->hours = round($out->diffInMinutes($in) / 60, 2);
        }
    } else {
        $action = 'already_done';
    }

    if ($action !== 'already_done') {
        $attendance->save();
    }

    return view('attendance.scan-result', [
        'employee' => $employee,
        'attendance' => $attendance,
        'action' => $action,
    ]);
})->name('attendance.scan');

Route::middleware(['auth'])->get('/reports/financial-word', function () {
    $income = Transaction::where('type', 'income')->where('status', 'confirmed')->sum('amount');
    $expense = Transaction::where('type', 'expense')->where('status', 'confirmed')->sum('amount');
    $monthIncome = Transaction::where('type', 'income')->whereYear('transaction_date', now()->year)->whereMonth('transaction_date', now()->month)->sum('amount');
    $monthExpense = Transaction::where('type', 'expense')->whereYear('transaction_date', now()->year)->whereMonth('transaction_date', now()->month)->sum('amount');

    $word = new PhpWord();
    $word->setDefaultFontName('Arial');
    $word->setDefaultFontSize(12);

    $section = $word->addSection(['rtl' => true]);
    $section->addTitle('Trust Guard — التقرير المالي', 1);
    $section->addText('تاريخ التقرير: '.now()->format('Y-m-d'), ['italic' => true]);
    $section->addTextBreak(1);

    $table = $section->addTable([
        'borderSize' => 6, 'borderColor' => 'cccccc', 'cellMargin' => 80, 'alignment' => 'center',
    ]);

    $row = function ($label, $value) use ($table) {
        $table->addRow();
        $table->addCell(4000)->addText($label, ['bold' => true]);
        $table->addCell(4000)->addText(number_format($value, 2).' ₪');
    };

    $row('إجمالي الإيرادات', $income);
    $row('إجمالي المصروفات', $expense);
    $row('الصافي', $income - $expense);
    $row('إيرادات هذا الشهر', $monthIncome);
    $row('مصروفات هذا الشهر', $monthExpense);
    $row('صافي هذا الشهر', $monthIncome - $monthExpense);

    $section->addTextBreak(2);
    $section->addText('Trust Guard — جميع الحقوق محفوظة', ['italic' => true, 'color' => '888888']);

    $tmp = storage_path('app/financial-report-'.now()->format('Y-m-d-His').'.docx');
    IOFactory::createWriter($word, 'Word2007')->save($tmp);

    return response()->download($tmp)->deleteFileAfterSend();
})->name('reports.financial-word');
