<script>
    import { onMount } from "svelte";
    import ApexCharts from "apexcharts";

    // Props desde el backend (puedes personalizarlas)
    let { data = null } = $props();

    let chartEl;
    let chartInstance;

    // Datos por defecto si no vienen del backend
    const defaultData = {
        newUsers: [5, 1, 17, 6, 15, 9, 6],
        users: [7, 11, 4, 16, 10, 14, 10],
        categories: ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"],
    };

    const chartData = data || defaultData;

    onMount(() => {
        const options = {
            series: [
                {
                    name: "New Users",
                    data: chartData.newUsers,
                },
                {
                    name: "Users",
                    data: chartData.users,
                },
            ],
            chart: {
                toolbar: {
                    show: false,
                },
                type: "line",
                fontFamily: "inherit",
                foreColor: "#adb0bb",
                height: 320,
                stacked: false,
            },
            colors: ["var(--bs-gray-300)", "var(--bs-primary)"],
            dataLabels: {
                enabled: false,
            },
            legend: {
                show: false,
            },
            stroke: {
                width: 2,
                curve: "smooth",
                dashArray: [8, 0],
            },
            grid: {
                borderColor: "rgba(0,0,0,0.1)",
                strokeDashArray: 3,
                xaxis: {
                    lines: {
                        show: false,
                    },
                },
            },
            xaxis: {
                axisBorder: {
                    show: false,
                },
                axisTicks: {
                    show: false,
                },
                categories: chartData.categories,
            },
            yaxis: {
                tickAmount: 4,
            },
            markers: {
                strokeColor: ["var(--bs-gray-300)", "var(--bs-primary)"],
                strokeWidth: 2,
            },
            tooltip: {
                theme: "dark",
            },
        };

        chartInstance = new ApexCharts(chartEl, options);
        chartInstance.render();

        // Cleanup cuando el componente se desmonte
        return () => {
            if (chartInstance) {
                chartInstance.destroy();
            }
        };
    });

    // Si los datos cambian, actualizar el gráfico
    $effect(() => {
        if (chartInstance && data) {
            chartInstance.updateSeries([
                { name: "New Users", data: data.newUsers },
                { name: "Users", data: data.users },
            ]);
        }
    });
</script>

<div class="card">
    <div class="card-body">
        <h5 class="card-title d-flex align-items-center gap-2 mb-4">
            Traffic Overview
            <span>
                <iconify-icon
                    icon="solar:question-circle-bold"
                    class="fs-7 d-flex text-muted"
                    data-bs-toggle="tooltip"
                    data-bs-title="Traffic Overview"
                ></iconify-icon>
            </span>
        </h5>
        <div bind:this={chartEl}></div>
    </div>
</div>
