<?php

namespace App\Services;

use App\Models\User;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Appointment;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class DashboardDataService
{
    protected int $cacheDuration = 300; // 5 minutos

    /**
     * Obtener todos los datos necesarios para el dashboard en una sola llamada
     */
    public function getDashboardData(User $user, bool $canViewAll = false): array
    {
        $cacheKey = "dashboard_data_" . ($canViewAll ? 'admin' : "user_{$user->id}");
        
        return Cache::remember($cacheKey, $this->cacheDuration, function () use ($user, $canViewAll) {
            $startDate = Carbon::now()->subMonths(12);
            $endDate = Carbon::now();
            
            $data = [
                'stats' => $this->getStatsData($user, $canViewAll, $startDate, $endDate),
                'revenue' => $this->getRevenueData($user, $canViewAll),
                'appointments' => $this->getAppointmentsData($user, $canViewAll),
                'monthly_revenue' => $this->getMonthlyRevenueData($user, $canViewAll),
                'top_ranking' => $this->getTopRankingData($user, $canViewAll),
            ];
            
            return $data;
        });
    }

    /**
     * Invalidar caché cuando hay cambios importantes
     */
    public function invalidateCache(?int $userId = null): void
    {
        Cache::forget('dashboard_data_admin');
        if ($userId) {
            Cache::forget("dashboard_data_user_{$userId}");
        }
    }

    /**
     * Datos para CombinedStatsOverview
     */
    protected function getStatsData(User $user, bool $canViewAll, Carbon $startDate, Carbon $endDate): array
    {
        if ($canViewAll) {
            return $this->getAdminStatsData($startDate, $endDate);
        }
        return $this->getProfessionalStatsData($user, $startDate, $endDate);
    }

    protected function getAdminStatsData(Carbon $startDate, Carbon $endDate): array
    {
        // Una sola query para obtener conteos
        $counts = DB::select("
            SELECT 
                (SELECT COUNT(*) FROM users) as total_professionals,
                (SELECT COUNT(*) FROM clients) as total_clients,
                (SELECT COUNT(*) FROM invoices WHERE created_at BETWEEN ? AND ?) as total_invoices
        ", [$startDate, $endDate]);

        $counts = $counts[0];

        // Datos históricos en paralelo usando subqueries
        $historicalData = DB::select("
            SELECT 
                'professionals' as type,
                EXTRACT(WEEK FROM created_at) as week,
                EXTRACT(YEAR FROM created_at) as year,
                COUNT(*) as total
            FROM users
            WHERE created_at BETWEEN ? AND ?
            GROUP BY week, year
            
            UNION ALL
            
            SELECT 
                'clients' as type,
                EXTRACT(WEEK FROM created_at) as week,
                EXTRACT(YEAR FROM created_at) as year,
                COUNT(*) as total
            FROM clients
            WHERE created_at BETWEEN ? AND ?
            GROUP BY week, year
            
            UNION ALL
            
            SELECT 
                'invoices' as type,
                EXTRACT(WEEK FROM created_at) as week,
                EXTRACT(YEAR FROM created_at) as year,
                COUNT(*) as total
            FROM invoices
            WHERE created_at BETWEEN ? AND ?
            GROUP BY week, year
            
            ORDER BY type, year, week
        ", [$startDate, $endDate, $startDate, $endDate, $startDate, $endDate]);

        $grouped = collect($historicalData)->groupBy('type');

        return [
            'total_professionals' => $counts->total_professionals,
            'total_clients' => $counts->total_clients,
            'total_invoices' => $counts->total_invoices,
            'professionals_chart' => $grouped->get('professionals', collect())->pluck('total')->map(fn($v) => (int)$v)->toArray(),
            'clients_chart' => $grouped->get('clients', collect())->pluck('total')->map(fn($v) => (int)$v)->toArray(),
            'invoices_chart' => $grouped->get('invoices', collect())->pluck('total')->map(fn($v) => (int)$v)->toArray(),
        ];
    }

    protected function getProfessionalStatsData(User $user, Carbon $startDate, Carbon $endDate): array
    {
        // Total de profesionales (igual que admin)
        $totalProfessionals = User::count();
        
        // Total de clientes (igual que admin)
        $totalClients = Client::count();

        // Datos históricos de profesionales
        $professionalsData = User::select(
            DB::raw('EXTRACT(WEEK FROM created_at) as week'),
            DB::raw('EXTRACT(YEAR FROM created_at) as year'),
            DB::raw('count(*) as total')
        )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('week', 'year')
            ->orderBy('year')
            ->orderBy('week')
            ->get()
            ->pluck('total')
            ->map(fn($v) => (int)$v)
            ->toArray();

        // Datos históricos de clientes
        $clientsData = Client::select(
            DB::raw('EXTRACT(WEEK FROM created_at) as week'),
            DB::raw('EXTRACT(YEAR FROM created_at) as year'),
            DB::raw('count(*) as total')
        )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('week', 'year')
            ->orderBy('year')
            ->orderBy('week')
            ->get()
            ->pluck('total')
            ->map(fn($v) => (int)$v)
            ->toArray();

        // Total de facturas del profesional (todas las históricas)
        $totalInvoices = Invoice::where('user_id', $user->id)->count();
        
        // Facturas del profesional para el gráfico (últimos 6 meses)
        $invoicesChart = Invoice::select(
            DB::raw('EXTRACT(WEEK FROM created_at) as week'),
            DB::raw('EXTRACT(YEAR FROM created_at) as year'),
            DB::raw('COUNT(*) as total')
        )
            ->where('user_id', $user->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('week', 'year')
            ->orderBy('year')
            ->orderBy('week')
            ->get()
            ->pluck('total')
            ->map(fn($v) => (int)$v)
            ->toArray();

        return [
            'total_professionals' => $totalProfessionals,
            'total_clients' => $totalClients,
            'total_invoices' => $totalInvoices,
            'professionals_chart' => $professionalsData,
            'clients_chart' => $clientsData,
            'invoices_chart' => $invoicesChart,
        ];
    }

    /**
     * Datos para RevenueOverviewWidget
     */
    protected function getRevenueData(User $user, bool $canViewAll): array
    {
        if ($canViewAll) {
            return $this->getAdminRevenueData();
        }
        return $this->getProfessionalRevenueData($user);
    }

    protected function getAdminRevenueData(): array
    {
        $currentMonth = Carbon::now();
        $previousMonth = Carbon::now()->subMonth();

        // Calcular ingresos de facturas considerando descuentos
        $invoiceRevenue = $this->calculateInvoiceRevenue(null, $currentMonth, $previousMonth);

        // Query optimizada para citas
        $appointmentData = DB::select("
            WITH appointment_revenue AS (
                SELECT 
                    SUM(service_price) as total,
                    SUM(CASE WHEN EXTRACT(MONTH FROM created_at) = ? AND EXTRACT(YEAR FROM created_at) = ? THEN service_price ELSE 0 END) as current_month,
                    SUM(CASE WHEN EXTRACT(MONTH FROM created_at) = ? AND EXTRACT(YEAR FROM created_at) = ? THEN service_price ELSE 0 END) as previous_month
                FROM appointments
                WHERE payment_status = 'paid'
                AND NOT EXISTS (
                    SELECT 1 FROM invoices 
                    WHERE invoices.appointment_id = appointments.id 
                    AND invoices.state = 'paid'
                )
            )
            SELECT 
                COALESCE(total, 0) as total,
                COALESCE(current_month, 0) as current_month,
                COALESCE(previous_month, 0) as previous_month
            FROM appointment_revenue
        ", [
            $currentMonth->month, $currentMonth->year,
            $previousMonth->month, $previousMonth->year
        ]);

        $appointmentRev = $appointmentData[0];

        // Sumar ingresos de facturas y citas
        $totalRevenue = $invoiceRevenue['total'] + $appointmentRev->total;
        $monthlyRevenue = $invoiceRevenue['current_month'] + $appointmentRev->current_month;
        $previousMonthRevenue = $invoiceRevenue['previous_month'] + $appointmentRev->previous_month;

        // Datos diarios para gráfico (últimos 30 días)
        $dailyRevenue = $this->getDailyRevenueChart(null);

        return [
            'total_revenue' => $totalRevenue,
            'monthly_revenue' => $monthlyRevenue,
            'previous_month_revenue' => $previousMonthRevenue,
            'growth_percentage' => $previousMonthRevenue > 0 
                ? (($monthlyRevenue - $previousMonthRevenue) / $previousMonthRevenue) * 100 
                : 0,
            'daily_chart' => $dailyRevenue,
            'active_professionals' => User::whereHas('roles')->count(),
        ];
    }

    protected function getProfessionalRevenueData(User $user): array
    {
        $currentMonth = Carbon::now();
        $previousMonth = Carbon::now()->subMonth();

        // Calcular ingresos de facturas considerando descuentos
        $invoiceRevenue = $this->calculateInvoiceRevenue($user->id, $currentMonth, $previousMonth);

        // Query optimizada para citas
        $appointmentData = DB::select("
            WITH appointment_revenue AS (
                SELECT 
                    SUM(service_price) as total,
                    SUM(CASE WHEN EXTRACT(MONTH FROM created_at) = ? AND EXTRACT(YEAR FROM created_at) = ? THEN service_price ELSE 0 END) as current_month
                FROM appointments
                WHERE payment_status = 'paid' AND user_id = ?
                AND NOT EXISTS (
                    SELECT 1 FROM invoices 
                    WHERE invoices.appointment_id = appointments.id 
                    AND invoices.state = 'paid'
                )
            )
            SELECT 
                COALESCE(total, 0) as total,
                COALESCE(current_month, 0) as current_month
            FROM appointment_revenue
        ", [
            $currentMonth->month, $currentMonth->year, $user->id
        ]);

        $appointmentRev = $appointmentData[0];

        // Sumar ingresos de facturas y citas
        $totalRevenue = $invoiceRevenue['total'] + $appointmentRev->total;
        $monthlyRevenue = $invoiceRevenue['current_month'] + $appointmentRev->current_month;

        $dailyRevenue = $this->getDailyRevenueChart($user->id);

        $appointmentsThisMonth = Appointment::where('user_id', $user->id)
            ->where('status', 'completed')
            ->whereMonth('created_at', $currentMonth->month)
            ->whereYear('created_at', $currentMonth->year)
            ->count();

        return [
            'total_revenue' => $totalRevenue,
            'monthly_revenue' => $monthlyRevenue,
            'daily_chart' => $dailyRevenue,
            'appointments_this_month' => $appointmentsThisMonth,
        ];
    }

    /**
     * Calcula el ingreso real de facturas considerando descuentos e impuestos
     * siguiendo la misma lógica del InvoiceResource
     */
    protected function calculateInvoiceRevenue(?int $userId, Carbon $currentMonth, Carbon $previousMonth): array
    {
        $query = Invoice::with('items')
            ->where('state', 'paid');
        
        if ($userId) {
            $query->where('user_id', $userId);
        }

        $invoices = $query->get();

        $totalRevenue = 0;
        $currentMonthRevenue = 0;
        $previousMonthRevenue = 0;

        foreach ($invoices as $invoice) {
            $invoiceTotal = $this->calculateInvoiceTotal($invoice);

            $totalRevenue += $invoiceTotal;

            // Verificar si es del mes actual
            if ($invoice->created_at->month == $currentMonth->month && 
                $invoice->created_at->year == $currentMonth->year) {
                $currentMonthRevenue += $invoiceTotal;
            }

            // Verificar si es del mes anterior
            if ($invoice->created_at->month == $previousMonth->month && 
                $invoice->created_at->year == $previousMonth->year) {
                $previousMonthRevenue += $invoiceTotal;
            }
        }

        return [
            'total' => $totalRevenue,
            'current_month' => $currentMonthRevenue,
            'previous_month' => $previousMonthRevenue,
        ];
    }

    /**
     * Calcula el total de una factura considerando descuentos e impuestos
     * Réplica de la lógica del InvoiceResource
     */
    protected function calculateInvoiceTotal(Invoice $invoice): float
    {
        try {
            // Calcular subtotal de todos los items
            $subtotal = \Brick\Money\Money::of(0, 'COP');

            foreach ($invoice->items as $item) {
                if ($item->unit_price instanceof \Brick\Money\Money) {
                    $itemTotal = $item->unit_price->multipliedBy($item->quantity);
                    $subtotal = $subtotal->plus($itemTotal);
                }
            }

            // Aplicar descuento si está habilitado
            if ($invoice->discount_enabled && $invoice->discount_percentage > 0) {
                $subtotal = $subtotal->multipliedBy(100 - $invoice->discount_percentage)
                    ->dividedBy(100, \Brick\Math\RoundingMode::HALF_UP);
            }

            // Calcular impuestos sobre el subtotal con descuento
            $totalTax = \Brick\Money\Money::of(0, 'COP');
            foreach ($invoice->items as $item) {
                if ($item->unit_price instanceof \Brick\Money\Money) {
                    $itemSubtotal = $item->unit_price->multipliedBy($item->quantity);

                    // Aplicar descuento al item si está habilitado
                    if ($invoice->discount_enabled && $invoice->discount_percentage > 0) {
                        $itemSubtotal = $itemSubtotal->multipliedBy(100 - $invoice->discount_percentage)
                            ->dividedBy(100, \Brick\Math\RoundingMode::HALF_UP);
                    }

                    $itemTax = $itemSubtotal->multipliedBy($item->tax_percentage ?? 19)
                        ->dividedBy(100, \Brick\Math\RoundingMode::HALF_UP);
                    $totalTax = $totalTax->plus($itemTax);
                }
            }

            $total = $subtotal->plus($totalTax);
            return $total->getAmount()->toFloat();
        } catch (\Exception $e) {
            // Fallback: usar el método totalAmount del modelo si existe
            if (method_exists($invoice, 'totalAmount')) {
                $money = $invoice->totalAmount();
                if ($money instanceof \Brick\Money\Money) {
                    return $money->getAmount()->toFloat();
                }
            }
            return 0;
        }
    }

    protected function getDailyRevenueChart(?int $userId): array
    {
        $startDate = Carbon::now()->subDays(30);
        $endDate = Carbon::now();

        // Obtener facturas pagadas de los últimos 30 días
        $invoiceQuery = Invoice::with('items')
            ->where('state', 'paid')
            ->whereBetween('created_at', [$startDate, $endDate]);
        
        if ($userId) {
            $invoiceQuery->where('user_id', $userId);
        }

        $invoices = $invoiceQuery->get();

        // Calcular ingresos por día de facturas (con descuentos)
        $invoicesByDay = [];
        foreach ($invoices as $invoice) {
            $date = $invoice->created_at->format('Y-m-d');
            $total = $this->calculateInvoiceTotal($invoice);
            
            if (!isset($invoicesByDay[$date])) {
                $invoicesByDay[$date] = 0;
            }
            $invoicesByDay[$date] += $total;
        }

        // Obtener citas pagadas (sin factura asociada)
        $appointmentQuery = Appointment::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(service_price) as total')
            )
            ->where('payment_status', 'paid')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNotExists(function($query) {
                $query->select(DB::raw(1))
                    ->from('invoices')
                    ->whereColumn('invoices.appointment_id', 'appointments.id')
                    ->where('invoices.state', 'paid');
            });
        
        if ($userId) {
            $appointmentQuery->where('user_id', $userId);
        }

        $appointments = $appointmentQuery->groupBy('date')->get();

        // Combinar ingresos de facturas y citas por día
        $dailyData = [];
        foreach ($appointments as $appointment) {
            $date = $appointment->date;
            $dailyData[$date] = ($invoicesByDay[$date] ?? 0) + $appointment->total;
        }

        // Agregar días que solo tienen facturas
        foreach ($invoicesByDay as $date => $total) {
            if (!isset($dailyData[$date])) {
                $dailyData[$date] = $total;
            }
        }

        // Ordenar por fecha y formatear
        ksort($dailyData);
        $result = [];
        foreach ($dailyData as $date => $total) {
            $result[] = (object)[
                'date' => $date,
                'total' => $total
            ];
        }

        return $result;
    }

    /**
     * Datos para los gráficos de citas
     */
    protected function getAppointmentsData(User $user, bool $canViewAll): array
    {
        $userFilter = $canViewAll ? "" : "WHERE user_id = {$user->id}";

        // Total de citas por fecha
        $appointmentsByDate = DB::select("
            SELECT 
                DATE(start_time) as date,
                COUNT(*) as total
            FROM appointments
            {$userFilter}
            GROUP BY DATE(start_time)
            ORDER BY date
        ");

        // Citas por estado
        $appointmentsByStatus = DB::select("
            SELECT 
                status,
                COUNT(*) as total
            FROM appointments
            {$userFilter}
            GROUP BY status
        ");

        return [
            'by_date' => collect($appointmentsByDate)->map(function($item) {
                return ['date' => $item->date, 'total' => (int)$item->total];
            })->toArray(),
            'by_status' => collect($appointmentsByStatus)->pluck('total', 'status')->map(fn($v) => (int)$v)->toArray(),
        ];
    }

    /**
     * Datos para MonthlyRevenueChart
     */
    protected function getMonthlyRevenueData(User $user, bool $canViewAll): array
    {
        $startDate = Carbon::now()->subMonths(12);

        // Obtener facturas pagadas de los últimos 12 meses
        $invoiceQuery = Invoice::with('items')
            ->where('state', 'paid')
            ->where('created_at', '>=', $startDate);
        
        if (!$canViewAll) {
            $invoiceQuery->where('user_id', $user->id);
        }

        $invoices = $invoiceQuery->get();

        // Calcular ingresos por mes de facturas (con descuentos)
        $invoicesByMonth = [];
        foreach ($invoices as $invoice) {
            $yearMonth = $invoice->created_at->format('Y-m');
            $total = $this->calculateInvoiceTotal($invoice);
            
            if (!isset($invoicesByMonth[$yearMonth])) {
                $invoicesByMonth[$yearMonth] = 0;
            }
            $invoicesByMonth[$yearMonth] += $total;
        }

        // Obtener citas pagadas (sin factura asociada)
        $appointmentQuery = Appointment::select(
                DB::raw('EXTRACT(YEAR FROM created_at) as year'),
                DB::raw('EXTRACT(MONTH FROM created_at) as month'),
                DB::raw('SUM(service_price) as total')
            )
            ->where('payment_status', 'paid')
            ->where('created_at', '>=', $startDate)
            ->whereNotExists(function($query) {
                $query->select(DB::raw(1))
                    ->from('invoices')
                    ->whereColumn('invoices.appointment_id', 'appointments.id')
                    ->where('invoices.state', 'paid');
            });
        
        if (!$canViewAll) {
            $appointmentQuery->where('user_id', $user->id);
        }

        $appointments = $appointmentQuery->groupBy('year', 'month')->get();

        // Generar array con todos los meses
        $result = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->startOfMonth()->subMonths($i);
            $yearMonth = $date->format('Y-m');
            
            // Buscar ingresos de facturas
            $invoiceTotal = $invoicesByMonth[$yearMonth] ?? 0;
            
            // Buscar ingresos de citas
            $appointmentTotal = 0;
            $found = $appointments->first(function($item) use ($date) {
                return $item->year == $date->year && $item->month == $date->month;
            });
            if ($found) {
                $appointmentTotal = $found->total;
            }
            
            $result[] = [
                'label' => $date->locale('es')->isoFormat('MMM YYYY'),
                'value' => (int)($invoiceTotal + $appointmentTotal),
            ];
        }

        return $result;
    }

    /**
     * Datos para TopProfessionalsChart o TopServicesChart
     */
    protected function getTopRankingData(User $user, bool $canViewAll): array
    {
        if ($canViewAll) {
            return $this->getTopProfessionals();
        }
        return $this->getTopServices($user);
    }

    protected function getTopProfessionals(): array
    {
        // Obtener todos los usuarios (profesionales)
        $users = User::with(['invoices' => function($query) {
            $query->with('items')->where('state', 'paid');
        }])->get();

        $professionalsRevenue = [];

        foreach ($users as $user) {
            $totalRevenue = 0;
            
            foreach ($user->invoices as $invoice) {
                $totalRevenue += $this->calculateInvoiceTotal($invoice);
            }

            if ($totalRevenue > 0) {
                $professionalsRevenue[] = [
                    'label' => $user->name,
                    'value' => $totalRevenue,
                ];
            }
        }

        // Ordenar por ingresos descendente y tomar top 10
        usort($professionalsRevenue, function($a, $b) {
            return $b['value'] <=> $a['value'];
        });

        return array_slice($professionalsRevenue, 0, 10);
    }

    protected function getTopServices(User $user): array
    {
        // Obtener servicios con sus citas e invoices del profesional
        $services = Service::with(['appointments' => function($query) use ($user) {
            $query->where('user_id', $user->id)
                  ->with(['invoices' => function($q) {
                      $q->with('items')->where('state', 'paid');
                  }]);
        }])->get();

        $servicesRevenue = [];

        foreach ($services as $service) {
            $totalRevenue = 0;
            
            foreach ($service->appointments as $appointment) {
                // Un appointment puede tener múltiples invoices
                foreach ($appointment->invoices as $invoice) {
                    $totalRevenue += $this->calculateInvoiceTotal($invoice);
                }
            }

            if ($totalRevenue > 0) {
                $servicesRevenue[] = [
                    'label' => $service->name,
                    'value' => $totalRevenue,
                ];
            }
        }

        // Ordenar por ingresos descendente y tomar top 10
        usort($servicesRevenue, function($a, $b) {
            return $b['value'] <=> $a['value'];
        });

        return array_slice($servicesRevenue, 0, 10);
    }
}
