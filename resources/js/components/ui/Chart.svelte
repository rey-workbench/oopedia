<script lang="ts">
    import { onMount, onDestroy } from 'svelte';

    interface Props {
        type?: string;
        series?: any[];
        options?: any;
        height?: number;
    }

    let { type = 'line', series = [], options = {}, height = 350 }: Props = $props();

    let chart: any;
    let chartElement: HTMLElement;

    async function initChart() {
        if (typeof window !== 'undefined') {
            const ApexCharts = (await import('apexcharts'));

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

    $effect(() => {
        if (chart && (series || options)) {
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
