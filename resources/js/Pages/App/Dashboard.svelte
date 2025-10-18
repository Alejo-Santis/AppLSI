<script>
    import AdminLayout from "@layouts/AdminLayout.svelte";
    import { Link } from "@inertiajs/svelte";
    import ApexCharts from "apexcharts";
    import { onMount } from "svelte";

    let {
        stats,
        financialStats,
        employeesByDept,
        employeesByLevel,
        employeesByStatus,
        hiringTimeline,
        recentHires,
        topDepartments,
        topPositions,
    } = $props();

    let deptChartEl, levelChartEl, statusChartEl, timelineChartEl;
    let deptChart, levelChart, statusChart, timelineChart;

    onMount(() => {
        // Gráfico: Empleados por Departamento
        deptChart = new ApexCharts(deptChartEl, {
            chart: {
                type: "bar",
                height: 320,
                toolbar: { show: false },
            },
            series: [
                {
                    name: "Empleados",
                    data: employeesByDept.map((d) => d.count),
                },
            ],
            xaxis: {
                categories: employeesByDept.map((d) => d.name),
                labels: {
                    rotate: -45,
                    rotateAlways: false,
                },
            },
            colors: ["#5D87FF"],
            plotOptions: {
                bar: {
                    borderRadius: 6,
                    columnWidth: "60%",
                },
            },
            dataLabels: {
                enabled: true,
            },
            title: {
                text: "Empleados por Departamento",
                align: "left",
                style: {
                    fontSize: "16px",
                    fontWeight: 600,
                },
            },
        });
        deptChart.render();

        // Gráfico: Distribución por Nivel
        levelChart = new ApexCharts(levelChartEl, {
            chart: {
                type: "donut",
                height: 320,
            },
            series: employeesByLevel.map((l) => l.count),
            labels: employeesByLevel.map((l) => l.level),
            colors: ["#5D87FF", "#49BEFF", "#13DEB9", "#FFAE1F", "#FA896B"],
            title: {
                text: "Distribución por Nivel",
                align: "left",
                style: {
                    fontSize: "16px",
                    fontWeight: 600,
                },
            },
            legend: {
                position: "bottom",
            },
            dataLabels: {
                enabled: true,
            },
            plotOptions: {
                pie: {
                    donut: {
                        size: "70%",
                    },
                },
            },
        });
        levelChart.render();

        // Gráfico: Estado de Empleados
        statusChart = new ApexCharts(statusChartEl, {
            chart: {
                type: "pie",
                height: 280,
            },
            series: employeesByStatus.map((s) => s.count),
            labels: employeesByStatus.map((s) => s.status),
            colors: ["#13DEB9", "#FA896B", "#FFAE1F"],
            legend: {
                position: "bottom",
            },
            dataLabels: {
                enabled: true,
            },
        });
        statusChart.render();

        // Gráfico: Timeline de Contrataciones
        timelineChart = new ApexCharts(timelineChartEl, {
            chart: {
                type: "area",
                height: 280,
                toolbar: { show: false },
            },
            series: [
                {
                    name: "Contrataciones",
                    data: hiringTimeline.map((h) => h.count),
                },
            ],
            xaxis: {
                categories: hiringTimeline.map((h) => h.month),
            },
            colors: ["#5D87FF"],
            fill: {
                type: "gradient",
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.7,
                    opacityTo: 0.3,
                },
            },
            dataLabels: {
                enabled: false,
            },
            stroke: {
                curve: "smooth",
                width: 2,
            },
            title: {
                text: "Contrataciones (Últimos 12 meses)",
                align: "left",
                style: {
                    fontSize: "16px",
                    fontWeight: 600,
                },
            },
        });
        timelineChart.render();

        return () => {
            deptChart?.destroy();
            levelChart?.destroy();
            statusChart?.destroy();
            timelineChart?.destroy();
        };
    });

    function getStatusBadge(status) {
        const badges = {
            active: "bg-light-success text-success",
            inactive: "bg-light-danger text-danger",
            on_leave: "bg-light-warning text-warning",
        };
        return badges[status] || "bg-light-secondary text-secondary";
    }

    function getStatusLabel(status) {
        const labels = {
            active: "Activo",
            inactive: "Inactivo",
            on_leave: "En Licencia",
        };
        return labels[status] || status;
    }
</script>

<AdminLayout title="Dashboard">
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-semibold mb-1">Panel de Control</h4>
                <p class="text-muted mb-0">
                    Bienvenido al sistema de gestión de empleados
                </p>
            </div>
            <div class="d-flex gap-2">
                <Link href="/employees/create" class="btn btn-primary">
                    <i class="bi bi-plus"></i> Nuevo Empleado
                </Link>
            </div>
        </div>

        <!-- Tarjetas de Estadísticas Principales -->
        <div class="row g-3 mb-4">
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div
                                class="rounded-circle bg-light-primary text-primary d-flex align-items-center justify-content-center"
                                style="width: 50px; height: 50px;"
                            >
                                <i class="bi bi-people fs-4"></i>
                            </div>
                            <div class="ms-3">
                                <small class="text-muted d-block"
                                    >Total Empleados</small
                                >
                                <h3 class="mb-0 fw-semibold">
                                    {stats.total_employees}
                                </h3>
                            </div>
                        </div>
                        <div class="mt-3">
                            <span class="badge bg-light-success text-success">
                                <i class="bi bi-arrow-up"></i>
                                {stats.active_employees} Activos
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div
                                class="rounded-circle bg-light-info text-info d-flex align-items-center justify-content-center"
                                style="width: 50px; height: 50px;"
                            >
                                <i class="bi bi-building fs-4"></i>
                            </div>
                            <div class="ms-3">
                                <small class="text-muted d-block"
                                    >Departamentos</small
                                >
                                <h3 class="mb-0 fw-semibold">
                                    {stats.total_departments}
                                </h3>
                            </div>
                        </div>
                        <div class="mt-3">
                            <span class="badge bg-light-info text-info">
                                {stats.active_departments} Activos
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div
                                class="rounded-circle bg-light-warning text-warning d-flex align-items-center justify-content-center"
                                style="width: 50px; height: 50px;"
                            >
                                <i class="bi bi-briefcase fs-4"></i>
                            </div>
                            <div class="ms-3">
                                <small class="text-muted d-block">Puestos</small
                                >
                                <h3 class="mb-0 fw-semibold">
                                    {stats.total_positions}
                                </h3>
                            </div>
                        </div>
                        <div class="mt-3">
                            <span class="badge bg-light-warning text-warning">
                                {stats.active_positions} Activos
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div
                                class="rounded-circle bg-light-success text-success d-flex align-items-center justify-content-center"
                                style="width: 50px; height: 50px;"
                            >
                                <i class="bi bi-cash-stack fs-4"></i>
                            </div>
                            <div class="ms-3">
                                <small class="text-muted d-block"
                                    >Nómina Total</small
                                >
                                <h3
                                    class="mb-0 fw-semibold"
                                    style="font-size: 1.3rem;"
                                >
                                    ${(
                                        financialStats.total_payroll || 0
                                    ).toLocaleString("es-CO", {
                                        minimumFractionDigits: 0,
                                        maximumFractionDigits: 0,
                                    })}
                                </h3>
                            </div>
                        </div>
                        <div class="mt-3">
                            <small class="text-muted">
                                Promedio: ${(
                                    financialStats.avg_salary || 0
                                ).toLocaleString("es-CO", {
                                    minimumFractionDigits: 0,
                                    maximumFractionDigits: 0,
                                })}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráficos Principales -->
        <div class="row g-3 mb-4">
            <!-- Empleados por Departamento -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div bind:this={deptChartEl}></div>
                    </div>
                </div>
            </div>

            <!-- Distribución por Nivel -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div bind:this={levelChartEl}></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráficos Secundarios -->
        <div class="row g-3 mb-4">
            <!-- Timeline de Contrataciones -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div bind:this={timelineChartEl}></div>
                    </div>
                </div>
            </div>

            <!-- Estado de Empleados -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Estado de Empleados</h5>
                        <div bind:this={statusChartEl}></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tablas de Información -->
        <div class="row g-3 mb-4">
            <!-- Contrataciones Recientes -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div
                            class="d-flex justify-content-between align-items-center mb-3"
                        >
                            <h5 class="card-title mb-0">
                                <i class="bi bi-clock-history"></i> Contrataciones
                                Recientes
                            </h5>
                            <Link
                                href="/employees"
                                class="btn btn-sm btn-outline-primary"
                            >
                                Ver Todos
                            </Link>
                        </div>

                        {#if recentHires.length > 0}
                            <div class="list-group list-group-flush">
                                {#each recentHires as hire}
                                    <Link
                                        href={`/employees/${hire.id}`}
                                        class="list-group-item list-group-item-action border-0 px-0"
                                    >
                                        <div
                                            class="d-flex justify-content-between align-items-start"
                                        >
                                            <div>
                                                <h6 class="mb-1">
                                                    {hire.full_name}
                                                </h6>
                                                <p
                                                    class="mb-1 text-muted small"
                                                >
                                                    {hire.position} - {hire.department}
                                                </p>
                                                <small class="text-muted">
                                                    <i
                                                        class="bi bi-calendar-check"
                                                    ></i>
                                                    {hire.hire_date_human}
                                                </small>
                                            </div>
                                            <span
                                                class="badge {getStatusBadge(
                                                    hire.status,
                                                )}"
                                            >
                                                {getStatusLabel(hire.status)}
                                            </span>
                                        </div>
                                    </Link>
                                {/each}
                            </div>
                        {:else}
                            <p class="text-muted text-center py-4">
                                No hay contrataciones recientes
                            </p>
                        {/if}
                    </div>
                </div>
            </div>

            <!-- Top Departamentos -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div
                            class="d-flex justify-content-between align-items-center mb-3"
                        >
                            <h5 class="card-title mb-0">
                                <i class="bi bi-trophy"></i> Top Departamentos
                            </h5>
                            <Link
                                href="/departments"
                                class="btn btn-sm btn-outline-primary"
                            >
                                Ver Todos
                            </Link>
                        </div>

                        {#if topDepartments.length > 0}
                            <div class="list-group list-group-flush">
                                {#each topDepartments as dept, index}
                                    <Link
                                        href={`/departments/${dept.id}`}
                                        class="list-group-item list-group-item-action border-0 px-0"
                                    >
                                        <div
                                            class="d-flex justify-content-between align-items-center"
                                        >
                                            <div
                                                class="d-flex align-items-center gap-3"
                                            >
                                                <div
                                                    class="rounded-circle bg-light-primary text-primary d-flex align-items-center justify-content-center fw-bold"
                                                    style="width: 35px; height: 35px;"
                                                >
                                                    {index + 1}
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">
                                                        {dept.name}
                                                    </h6>
                                                    <small class="text-muted">
                                                        {dept.code} • Manager: {dept.manager}
                                                    </small>
                                                </div>
                                            </div>
                                            <span
                                                class="badge bg-light-secondary text-secondary"
                                            >
                                                {dept.employees_count} empleados
                                            </span>
                                        </div>
                                    </Link>
                                {/each}
                            </div>
                        {:else}
                            <p class="text-muted text-center py-4">
                                No hay departamentos
                            </p>
                        {/if}
                    </div>
                </div>
            </div>
        </div>

        <!-- Accesos Rápidos -->
        <div class="row g-3">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-3">
                            <i class="bi bi-lightning"></i> Accesos Rápidos
                        </h5>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <Link
                                    href="/employees/create"
                                    class="btn btn-outline-primary w-100 py-3"
                                >
                                    <i
                                        class="bi bi-person-plus fs-4 d-block mb-2"
                                    ></i>
                                    Nuevo Empleado
                                </Link>
                            </div>
                            <div class="col-md-3">
                                <Link
                                    href="/departments/create"
                                    class="btn btn-outline-info w-100 py-3"
                                >
                                    <i
                                        class="bi bi-building-add fs-4 d-block mb-2"
                                    ></i>
                                    Nuevo Departamento
                                </Link>
                            </div>
                            <div class="col-md-3">
                                <Link
                                    href="/positions/create"
                                    class="btn btn-outline-warning w-100 py-3"
                                >
                                    <i
                                        class="bi bi-briefcase-fill fs-4 d-block mb-2"
                                    ></i>
                                    Nuevo Puesto
                                </Link>
                            </div>
                            <div class="col-md-3">
                                <Link
                                    href="/employees"
                                    class="btn btn-outline-success w-100 py-3"
                                >
                                    <i class="bi bi-list-ul fs-4 d-block mb-2"
                                    ></i>
                                    Ver Empleados
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</AdminLayout>
