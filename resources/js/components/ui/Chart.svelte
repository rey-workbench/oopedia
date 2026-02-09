<script>
    import { onMount, afterUpdate, onDestroy } from "svelte";

    export let type = "line";
    export let series = [];
    export let options = {};
    export let height = 350;

    let chart;
    let chartElement;

    async function initChart() {
        if (typeof window !== "undefined") {
            const ApexCharts = (await import("apexcharts")).default;

            const config = {
                series: series,
                chart: {
                    type: type,
                    height: height,
                    ...options.chart,
                },
                ...options,
            };

            if (chart) {
                chart.destroy();
            }

            chart = new ApexCharts(chartElement, config);
            chart.render();
        }
    }

    onMount(() => {
        initChart();
    });

    afterUpdate(() => {
        if (chart) {
            chart.updateOptions({
                ...options,
                series: series,
            });
        }
    });

    onDestroy(() => {
        if (chart) {
            chart.destroy();
        }
    });
</script>

<div bind:this={chartElement}></div>
