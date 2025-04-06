<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { Line } from 'vue-chartjs';
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  LineElement,
  CategoryScale,
  LinearScale,
  PointElement,
  ChartOptions
} from 'chart.js';


ChartJS.register(Title, Tooltip, Legend, LineElement, CategoryScale, LinearScale, PointElement);

const chartData = ref({
  labels: [],
  datasets: [{
    label: 'Total Transaction (IDR)',
    data: [],
    borderColor: 'rgb(75, 192, 192)',
    backgroundColor: 'rgba(75, 192, 192, 0.2)',
    fill: true,
    tension: 0.4
  }]
});


const chartOptions = ref<ChartOptions<'line'>>({
  responsive: true,
  maintainAspectRatio: true,
  plugins: {
    legend: { position: 'top' },
  }
});


onMounted(async () => {
  const response = await fetch('/api/transactions/get-chart');
  const result = await response.json();

  console.log(result);

  chartData.value = {
    labels: result.data.labels,
    datasets: [{
      label: 'Total Transaction (IDR)',
      data: result.data.data,
      borderColor: 'rgb(75, 192, 192)',
      backgroundColor: 'rgba(75, 192, 192, 0.2)',
      fill: true,
      tension: 0.4
    }]
  };
});
</script>

<template>
  <Line :data="chartData" :options="chartOptions"/>
</template>
