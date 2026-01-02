<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Rental;
use App\Models\Field;
use App\Models\Item;
use App\Models\Slot;
use App\Models\Report;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class ReportController extends Controller
{
    /**
     * Helper to determine user role from session and get the correct view context.
     * @return object
     */
    private function getViewContext()
    {
        $userType = session('user_type');
        
        if (session()->has('user_id')) {
            if ($userType === 'administrator') {
                return (object)[
                    'user_type_prefix' => 'admin', // for routes like 'admin.reports.index'
                    'view_path' => 'Report.admin' // for views in 'report/admin/'
                ];
            }
            if ($userType === 'staff') {
                return (object)[
                    'user_type_prefix' => 'staff', // for routes like 'staff.reports.index'
                    'view_path' => 'Report.staff' // for views in 'report/staff/'
                ];
            }
        }
        
        // Fallback
        return (object)[
            'user_type_prefix' => 'admin',
            'view_path' => 'Report.admin'
        ];
    }

    /**
     * Display the main report dashboard (KPIs and automated charts ONLY).
     */
    public function index()
{
    $viewContext = $this->getViewContext();
    $now = Carbon::now('Asia/Kuala_Lumpur');

    // Month boundaries (IMPORTANT)
    $startOfMonth = $now->copy()->startOfMonth();
    $startOfNextMonth = $now->copy()->addMonth()->startOfMonth();

    /*
    |--------------------------------------------------------------------------
    | PART 1: KPIs (CURRENT MONTH – SERVICE DATE BASED)
    |--------------------------------------------------------------------------
    */

    // ✅ 1. BOOKING REVENUE (BASED ON SLOT DATE)
    $kpi_revenue = Payment::whereNotNull('payment.bookingID')
        ->whereIn('payment.payment_Status', [
            'paid',
            'paid_balance',
            'paid_balance (cash)'
        ])
        ->join('booking', 'payment.bookingID', '=', 'booking.bookingID')
        ->join('slot', 'booking.slotID', '=', 'slot.slotID')
        ->whereBetween('slot.slot_Date', [$startOfMonth, $startOfNextMonth])
        ->sum('payment.payment_Amount');

    // ✅ 2. TOTAL BOOKINGS (BASED ON SLOT DATE)
    $kpi_bookings = Booking::whereIn('booking.booking_Status', ['paid', 'completed'])
        ->join('slot', 'booking.slotID', '=', 'slot.slotID')
        ->whereBetween('slot.slot_Date', [$startOfMonth, $startOfNextMonth])
        ->count();

    // ✅ 3. RENTAL REVENUE (BASED ON RENTAL START DATE, FROM PAYMENT TABLE)
    $kpi_rental_revenue = Payment::whereNotNull('payment.rentalID')
        ->whereIn('payment.payment_Status', [
            'paid',
            'paid_balance',
            'paid_balance (cash)'
        ])
        ->join('rental', 'payment.rentalID', '=', 'rental.rentalID')
        ->whereBetween('rental.rental_StartDate', [$startOfMonth, $startOfNextMonth])
        ->sum('payment.payment_Amount');

    // ✅ 4. TOTAL ITEMS RENTED (BASED ON RENTAL START DATE)
    $kpi_items_rented = Rental::where('rental_Status', 'paid')
        ->whereBetween('rental_StartDate', [$startOfMonth, $startOfNextMonth])
        ->sum('quantity');

    /*
    |--------------------------------------------------------------------------
    | PART 2: MONTHLY BOOKING REVENUE CHART (LAST 6 MONTHS)
    |--------------------------------------------------------------------------
    | (UNCHANGED)
    |--------------------------------------------------------------------------
    */

    $sixMonthsAgo = $now->copy()->subMonths(5)->startOfMonth();

    $monthlyRevenueData = Payment::whereNotNull('payment.bookingID')
        ->whereIn('payment.payment_Status', [
            'paid',
            'paid_balance',
            'paid_balance (cash)'
        ])
        ->join('booking', 'payment.bookingID', '=', 'booking.bookingID')
        ->join('slot', 'booking.slotID', '=', 'slot.slotID')
        ->where('slot.slot_Date', '>=', $sixMonthsAgo)
        ->select(
            DB::raw('SUM(payment.payment_Amount) as total_revenue'),
            DB::raw('DATE_FORMAT(slot.slot_Date, "%Y-%m") as month')
        )
        ->groupBy('month')
        ->orderBy('month', 'asc')
        ->get();

    $chartLabels = [];
    $chartData = [];

    for ($i = 0; $i < 6; $i++) {
        $period = $now->copy()->subMonths(5 - $i);
        $monthKey = $period->format('Y-m');

        $chartLabels[] = $period->format('M Y');
        $monthData = $monthlyRevenueData->firstWhere('month', $monthKey);
        $chartData[] = $monthData ? (float) $monthData->total_revenue : 0;
    }

    /*
    |--------------------------------------------------------------------------
    | PART 3: TOP RENTED ITEMS (LAST 90 DAYS)
    |--------------------------------------------------------------------------
    | (UNCHANGED)
    |--------------------------------------------------------------------------
    */

    $topItemsData = Rental::where('rental.rental_Status', 'paid')
        ->where('rental.rental_StartDate', '>=', $now->copy()->subDays(90))
        ->join('item', 'rental.itemID', '=', 'item.itemID')
        ->select(
            'item.item_Name',
            DB::raw('SUM(rental.quantity) as total_rented')
        )
        ->groupBy('item.itemID', 'item.item_Name')
        ->orderBy('total_rented', 'desc')
        ->limit(5)
        ->get();

    return view($viewContext->view_path . '.MainReportPage', [
        'kpi_revenue' => $kpi_revenue,
        'kpi_bookings' => $kpi_bookings,
        'kpi_rental_revenue' => $kpi_rental_revenue,
        'kpi_items_rented' => $kpi_items_rented,
        'chartLabels' => json_encode($chartLabels),
        'chartData' => json_encode($chartData),
        'topItemsLabels' => json_encode($topItemsData->pluck('item_Name')),
        'topItemsQuantities' => json_encode($topItemsData->pluck('total_rented')),
    ]);
}



    /**
     * Show the form for creating a new custom Report.
     */
    public function create(Request $request)
    {
        $viewContext = $this->getViewContext();
        $fields = Field::all();
        $items = Item::all();
        $formDefaults = $request->query();

        // --- THIS IS THE FIX: Return the correct view based on role ---
        return view($viewContext->view_path . '.addPage', compact('fields', 'items', 'formDefaults'));
    }

    /**
     * Generate and show the custom report result.
     */
    public function show(Request $request)
    {
        $viewContext = $this->getViewContext();
        
        // 1. Validation
        $request->validate([
            'report_type' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        // 2. Variable Setup (Unchanged)
        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();
        $reportType = $request->report_type;
        $groupBy = $request->input('group_by', 'day');
        $customReportTitle = ucwords(str_replace('_', ' ', $reportType)) . " from " . $startDate->format('M j, Y') . " to " . $endDate->format('M j, Y');
        
        // 3. Determine Report Category & Initialize (Unchanged)
        $isBookingReport = in_array($reportType, ['booking_revenue', 'booking_count', 'field_performance', 'peak_hours']);
        $isRentalReport = in_array($reportType, ['rental_revenue', 'item_popularity']);
        $results = collect();

        // 4. Build & Execute Queries (Unchanged - this logic is identical for admin/staff)
        if ($isBookingReport) {
            $query = Slot::whereBetween('slot_Date', [$startDate, $endDate]);
            $dateColumn = 'slot_Date';
            if ($request->filled('field_id') && $reportType !== 'field_performance') {
                $query->where('slot.fieldID', $request->field_id);
                $field = Field::find($request->field_id);
                if ($field) $customReportTitle .= ' for ' . $field->field_Label;
            }
            switch ($reportType) {
                case 'booking_revenue':
                case 'booking_count':
                    $dateFormat = match ($groupBy) { 'month' => '%Y-%m', 'week' => '%Y-%u', default => '%Y-%m-%d' };
                    $value_expression = ($reportType === 'booking_revenue') ? 'SUM(slot.slot_Price)' : 'COUNT(*)';
                    $query->join('booking', 'slot.slotID', '=', 'booking.slotID')->where('booking.booking_Status', 'paid')->select(DB::raw("DATE_FORMAT($dateColumn, '$dateFormat') as period"), DB::raw("$value_expression as value"));
                    $results = $query->groupBy('period')->orderBy('period', 'asc')->get();
                    break;
                case 'field_performance':
                    $query->join('booking', 'slot.slotID', '=', 'booking.slotID')->join('field', 'slot.fieldID', '=', 'field.fieldID')->where('booking.booking_Status', 'paid')->select('field.field_Label as label', DB::raw('COUNT(booking.bookingID) as value'));
                    $results = $query->groupBy('label')->orderBy('value', 'desc')->get();
                    break;
                case 'peak_hours':
                    $query->join('booking', 'slot.slotID', '=', 'booking.slotID')->where('booking.booking_Status', 'paid')->select('slot.slot_Time as label', DB::raw('COUNT(booking.bookingID) as value'));
                    $results = $query->groupBy('label')->get();
                    break;
            }
        }
        if ($isRentalReport) {
            $query = Rental::where('rental_Status', 'paid')->where('rental_StartDate', '<=', $endDate)->where('rental_EndDate', '>=', $startDate);
            if ($request->filled('item_id')) {
                $query->where('rental.itemID', $request->item_id);
                $item = Item::find($request->item_id);
                if ($item) $customReportTitle .= ' for ' . $item->item_Name;
            }
            switch ($reportType) {
                case 'rental_revenue':
                    $results = $query->with('item')->get();
                    break;
                case 'item_popularity':
                    $query->join('item', 'rental.itemID', '=', 'item.itemID')->select('item.item_Name as label', DB::raw('SUM(rental.quantity) as value'));
                    $results = $query->groupBy('label')->orderBy('value', 'desc')->get();
                    break;
            }
        }
        
        // 5. Prepare Data for Chart.js (Unchanged)
        $isCategorical = in_array($reportType, ['field_performance', 'peak_hours', 'item_popularity']);
        if ($reportType === 'rental_revenue') {
            $dailyRevenue = [];
            $period = new \DatePeriod(clone $startDate, new \DateInterval('P1D'), clone $endDate->addDay());
            foreach ($period as $date) { $dailyRevenue[$date->format('Y-m-d')] = 0; }
            foreach ($results as $rental) {
                if (!$rental->item) continue;
                $rentalStart = Carbon::parse($rental->rental_StartDate);
                $rentalEnd = Carbon::parse($rental->rental_EndDate);
                $rentalDays = $rentalStart->diffInDays($rentalEnd) + 1;
                $totalPrice = $rental->quantity * $rental->item->item_Price * $rentalDays;
                $dailyPrice = ($rentalDays > 0) ? $totalPrice / $rentalDays : 0;
                $rentalPeriod = new \DatePeriod($rentalStart, new \DateInterval('P1D'), $rentalEnd->addDay());
                foreach ($rentalPeriod as $day) {
                    $dayString = $day->format('Y-m-d');
                    if (isset($dailyRevenue[$dayString])) {
                        $dailyRevenue[$dayString] += $dailyPrice;
                    }
                }
            }
            $customChartLabels = json_encode(array_map(fn($date) => Carbon::parse($date)->format('M j'), array_keys($dailyRevenue)));
            $customChartData = json_encode(array_values($dailyRevenue));
        } elseif ($isCategorical) {
            if ($reportType === 'peak_hours') {
                $allTimeSlots = ['08:00:00', '10:00:00', '12:00:00', '14:00:00', '16:00:00', '18:00:00', '20:00:00', '22:00:00'];
                $chartLabels = []; $chartData = [];
                foreach ($allTimeSlots as $time) {
                    $chartLabels[] = Carbon::parse($time)->format('h:i A');
                    $resultForTime = $results->firstWhere('label', $time);
                    $chartData[] = $resultForTime ? $resultForTime->value : 0;
                }
                $customChartLabels = json_encode($chartLabels);
                $customChartData = json_encode($chartData);
            } else {
                $customChartLabels = json_encode($results->pluck('label'));
                $customChartData = json_encode($results->pluck('value'));
            }
        } else {
            $labels = match ($groupBy) {
                'week' => $results->pluck('period')->map(fn($p) => "Week " . substr($p, 5, 2)),
                'month' => $results->pluck('period')->map(fn($p) => Carbon::parse($p)->format('M Y')),
                default => $results->pluck('period')->map(fn($p) => Carbon::parse($p)->format('M j')),
            };
            $customChartLabels = json_encode($labels);
            $customChartData = json_encode($results->pluck('value'));
        }

        // 6. Calculate Summary Data (Unchanged)
        $summaryData = [];
        $dataValues = collect(json_decode($customChartData));
        $labelValues = collect(json_decode($customChartLabels));
        if ($dataValues->isNotEmpty()) {
            $sum = $dataValues->sum();
            $peakValue = $dataValues->max();
            $peakIndices = $dataValues->keys()->filter(fn($key) => $dataValues[$key] == $peakValue);
            switch ($reportType) {
                case 'booking_revenue':
                case 'rental_revenue':
                    $divisor = $endDate->diffInDays($startDate) + 1;
                    $average = ($divisor > 0) ? $sum / $divisor : 0;
                    $summaryData['total'] = "RM " . number_format($sum, 2);
                    $summaryData['average'] = "RM " . number_format($average, 2);
                    if ($peakIndices->isNotEmpty()) {
                        $peakLabels = $peakIndices->map(fn($index) => $labelValues[$index])->implode(', ');
                        $summaryData['peak_period'] = $peakLabels . " (RM " . number_format($peakValue, 2) . ")";
                    }
                    break;
                case 'booking_count':
                    $divisor = $endDate->diffInDays($startDate) + 1;
                    $average = ($divisor > 0) ? $sum / $divisor : 0;
                    $summaryData['total'] = intval($sum) . " Bookings";
                    $summaryData['average'] = number_format($average, 1);
                    if ($peakIndices->isNotEmpty()) {
                        $peakLabels = $peakIndices->map(fn($index) => $labelValues[$index])->implode(', ');
                        $summaryData['peak_period'] = $peakLabels . " (" . intval($peakValue) . " bookings)";
                    }
                    break;
                case 'field_performance':
                case 'item_popularity':
                    $summaryData['total'] = intval($sum) . ($reportType === 'field_performance' ? " Bookings" : " Items Rented");
                    if ($peakIndices->isNotEmpty()) {
                        $peakLabels = $peakIndices->map(fn($index) => $labelValues[$index])->implode(', ');
                        $summaryData['most_popular'] = $peakLabels . " (" . intval($peakValue) . ")";
                    }
                    break;
                case 'peak_hours':
                    $summaryData['total'] = intval($sum) . " Bookings";
                    if ($peakIndices->isNotEmpty()) {
                        $peakLabels = $peakIndices->map(fn($index) => $labelValues[$index])->implode(', ');
                        $summaryData['busiest_time'] = $peakLabels . " (" . intval($peakValue) . " bookings)";
                    }
                    break;
            }
        }
        
        // 7. Return the View
        // --- THIS IS THE FIX: Return the correct view based on role ---
        return view($viewContext->view_path . '.reportResult', [
            'customReportTitle' => $customReportTitle,
            'customChartLabels' => $customChartLabels,
            'customChartData' => $customChartData,
            'reportType' => $reportType,
            'request' => $request,
            'summaryData' => $summaryData,
            'groupBy' => $groupBy,
        ]);
    }

    public function publish(Request $request)
    {
        $viewContext = $this->getViewContext();

        $request->validate([
            'report_Title' => 'required|string|max:50',
            'report_type' => 'required|string',
        ]);

        $parameters = [
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'group_by' => $request->group_by,
            'field_id' => $request->field_id,
            'item_id' => $request->item_id,
        ];

        Report::create([
            'reportID' => 'REP' . strtoupper(uniqid()),
            'report_Title' => $request->report_Title,
            'report_type' => $request->report_type,
            'parameters' => $parameters,
            'published_by_user_id' => session('user_id'),
        ]);

        // --- THIS IS THE FIX: Redirect to the correct route prefix ---
        return redirect()->route($viewContext->user_type_prefix . '.reports.published')
                         ->with('success', 'Report has been published successfully!');
    }

    /**
     * Display a list of all published reports.
     */
    public function publishedList()
    {
        $viewContext = $this->getViewContext();
        $savedReports = Report::with('publisher')->latest('reportID')->paginate(5);
        
        // --- THIS IS THE FIX: Return the correct view based on role ---
        return view($viewContext->view_path . '.publishedList', compact('savedReports'));
    }

    /**
    * Generate a booking demand forecast by calling the Python AI API.
    */
    public function getBookingForecast()
    {
        // This function is universal and doesn't need to change
        try {
            $response = Http::timeout(10)->get('http://127.0.0.1:5000/predict');

            if ($response->successful()) {
                return $response->json();
            } else {
                \Log::error('AI API request failed: ' . $response->body());
                return response()->json(['error' => 'Failed to retrieve forecast data.'], 500);
            }

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            \Log::error('Could not connect to AI API: ' . $e->getMessage());
            return response()->json(['error' => 'The forecast service is currently unavailable.'], 503);
        }
    }
}
