<script>
    import AdminLayout from "@layouts/AdminLayout.svelte";
    import ApexCharts from "apexcharts";
    import { onMount } from "svelte";

    let { stats, employeesByDept, employeesByLevel } = $props();

    // Convertimos stats a un arreglo [{label: ..., value: ...}]
    let statList = Object.entries(stats).map(([key, value]) => ({
        label: key.replaceAll("_", " "),
        value,
    }));

    let deptChartEl, levelChartEl;
    let deptChart, levelChart;

    onMount(() => {
        deptChart = new ApexCharts(deptChartEl, {
            chart: { type: "bar", height: 300, toolbar: { show: false } },
            series: [
                {
                    name: "Empleados",
                    data: employeesByDept.map((d) => d.count),
                },
            ],
            xaxis: { categories: employeesByDept.map((d) => d.name) },
            colors: ["#6366f1"],
            title: { text: "Empleados por Departamento", align: "center" },
        });
        deptChart.render();

        levelChart = new ApexCharts(levelChartEl, {
            chart: { type: "donut", height: 280 },
            series: employeesByLevel.map((l) => l.count),
            labels: employeesByLevel.map((l) => l.level),
            colors: ["#3b82f6", "#10b981", "#f59e0b", "#ef4444", "#8b5cf6"],
            title: {
                text: "Distribución por Nivel de Puesto",
                align: "center",
            },
            legend: { position: "bottom" },
        });
        levelChart.render();

        return () => {
            deptChart?.destroy();
            levelChart?.destroy();
        };
    });
</script>

<AdminLayout>
    <div class="container-fluid">
        <div class="row g-4 mb-4">
            {#each statList as stat}
                <div class="col-sm-6 col-lg-3">
                    <div class="card text-center shadow-sm border-0">
                        <div class="card-body">
                            <h5 class="card-title text-muted text-capitalize">
                                <i class="bi bi-file-bar-graph"></i>
                                {stat.label}
                            </h5>
                            <h2 class="fw-bold">{stat.value}</h2>
                        </div>
                    </div>
                </div>
            {/each}
        </div>

        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <div bind:this={deptChartEl}></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <div bind:this={levelChartEl}></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</AdminLayout>
