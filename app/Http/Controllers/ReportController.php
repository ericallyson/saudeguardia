<?php

namespace App\Http\Controllers;

use App\Services\ReportPdfBuilder;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function __construct(
        private readonly ReportService $reportService,
        private readonly ReportPdfBuilder $reportPdfBuilder,
    ) {
    }

    /**
     * @return View|StreamedResponse
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        $startDate = isset($validated['start_date'])
            ? Carbon::parse($validated['start_date'])->startOfDay()
            : Carbon::now()->subDays(30)->startOfDay();
        $endDate = isset($validated['end_date'])
            ? Carbon::parse($validated['end_date'])->endOfDay()
            : Carbon::now()->endOfDay();

        $report = $this->reportService->buildForPeriod((int) Auth::id(), $startDate, $endDate);

        if ($request->input('export') === 'pdf') {
            $pdf = $this->reportPdfBuilder->build($report);
            $fileName = sprintf('relatorio-geral-%s.pdf', Carbon::now()->format('YmdHis'));

            return response()->streamDownload(fn () => print $pdf, $fileName, [
                'Content-Type' => 'application/pdf',
            ]);
        }

        return view('reports.index', [
            'report' => $report,
            'filters' => [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
            ],
        ]);
    }
}
