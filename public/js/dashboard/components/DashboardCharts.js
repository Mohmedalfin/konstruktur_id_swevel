import { getState } from "../core/state.js";

let progressChart = null;
let costChart = null;

function formatCompactRupiah(value) {
  if (value === null || value === undefined || isNaN(value)) {
    return "Rp 0";
  }

  return new Intl.NumberFormat("id-ID", {
    style: "currency",
    currency: "IDR",
    notation: "compact",
    compactDisplay: "short",
    minimumFractionDigits: 0,
    maximumFractionDigits: 1,
  }).format(value);
}

export function renderProgressChart() {
  const { data } = getState();
  const elContainer = document.getElementById("chart-progress");

  if (!elContainer || !data || !data.chart) return;

  const chartData = data.chart;

  if (progressChart) {
    progressChart.destroy();
    progressChart = null;
  }

  elContainer.innerHTML = "";

  const options = {
    series: [
      {
        name: "Planned Progress",
        data: chartData.planned,
      },
      {
        name: "Actual Progress",
        data: chartData.actual,
      },
    ],
    chart: {
      type: "area",
      height: 300,
      fontFamily: "inherit",
      toolbar: {
        show: false,
      },
      zoom: {
        enabled: false,
      },
      animations: {
        enabled: true,
        easing: "easeinout",
        speed: 800,
        animateGradually: {
          enabled: true,
          delay: 150,
        },
        dynamicAnimation: {
          enabled: true,
          speed: 350,
        },
      },
    },
    colors: ["#10b981", "#3b82f6"],
    dataLabels: {
      enabled: false,
    },
    stroke: {
      curve: "straight",
      width: [2, 2],
      dashArray: [0, 0],
    },
    fill: {
      type: "gradient",
      gradient: {
        shadeIntensity: 1,
        opacityFrom: 0.4,
        opacityTo: 0.05,
        stops: [0, 90, 100],
      },
    },
    markers: {
      size: 4,
      colors: ["#fff", "#fff"],
      strokeColors: ["#10b981", "#3b82f6"],
      strokeWidth: 2,
      hover: {
        size: 6,
      },
    },
    xaxis: {
      categories: chartData.labels,
      title: {
        text: "Week",
        style: {
          fontSize: "10px",
          fontWeight: 600,
          color: "#94a3b8",
        },
      },
      axisBorder: {
        show: false,
      },
      axisTicks: {
        show: false,
      },
      labels: {
        style: {
          colors: "#94a3b8",
          fontSize: "10px",
          fontWeight: 500,
        },
        formatter: function (value) {
          return value ? value.replace("W", "") : "";
        },
      },
    },
    yaxis: {
      min: 0,
      max: 100,
      tickAmount: 5,
      labels: {
        formatter: function (value) {
          return Math.round(value) + "%";
        },
        style: {
          colors: "#94a3b8",
          fontSize: "10px",
          fontWeight: 500,
        },
      },
    },
    grid: {
      borderColor: "#f1f5f9",
      strokeDashArray: 4,
      xaxis: {
        lines: {
          show: true,
        },
      },
      yaxis: {
        lines: {
          show: true,
        },
      },
      padding: {
        top: 0,
        right: 0,
        bottom: 0,
        left: 15,
      },
    },
    legend: {
      position: "bottom",
      horizontalAlign: "center",
      markers: {
        radius: 12,
      },
      itemMargin: {
        horizontal: 10,
        vertical: 10,
      },
      labels: {
        colors: "#64748b",
      },
    },
    tooltip: {
      theme: "light",
      y: {
        formatter: function (val) {
          return val !== null ? val + "%" : "Belum terjadi";
        },
      },
    },
  };

  progressChart = new ApexCharts(elContainer, options);
  progressChart.render();
}

export function renderCostChart() {
  const { data } = getState();
  const elContainer = document.getElementById("chart-cost");

  if (!elContainer || !data || !data.cost_chart) return;

  const chartData = data.cost_chart;

  if (costChart) {
    costChart.destroy();
    costChart = null;
  }

  elContainer.innerHTML = "";

  const options = {
    series: [
      {
        name: "Planned Cost",
        data: chartData.planned || [],
      },
      {
        name: "Actual Cost",
        data: chartData.actual || [],
      },
    ],
    chart: {
      type: "area",
      height: 300,
      fontFamily: "inherit",
      toolbar: {
        show: false,
      },
      zoom: {
        enabled: false,
      },
      animations: {
        enabled: true,
        easing: "easeinout",
        speed: 800,
      },
    },
    colors: ["#a855f7", "#0ea5e9"],
    dataLabels: {
      enabled: false,
    },
    stroke: {
      curve: "straight",
      width: [2, 2],
      dashArray: [0, 0],
    },
    fill: {
      type: "gradient",
      gradient: {
        shadeIntensity: 1,
        opacityFrom: 0.35,
        opacityTo: 0.05,
        stops: [0, 90, 100],
      },
    },
    markers: {
      size: 4,
      colors: ["#fff", "#fff"],
      strokeColors: ["#a855f7", "#0ea5e9"],
      strokeWidth: 2,
      hover: {
        size: 6,
      },
    },
    xaxis: {
      categories: chartData.labels || [],
      title: {
        text: "Week",
        style: {
          fontSize: "10px",
          fontWeight: 600,
          color: "#94a3b8",
        },
      },
      axisBorder: {
        show: false,
      },
      axisTicks: {
        show: false,
      },
      labels: {
        style: {
          colors: "#94a3b8",
          fontSize: "10px",
          fontWeight: 500,
        },
        formatter: function (value) {
          return value ? value.replace("W", "") : "";
        },
      },
    },
    yaxis: {
      min: 0,
      tickAmount: 5,
      labels: {
        formatter: function (value) {
          return formatCompactRupiah(value);
        },
        style: {
          colors: "#94a3b8",
          fontSize: "10px",
          fontWeight: 500,
        },
      },
    },
    grid: {
      borderColor: "#f1f5f9",
      strokeDashArray: 4,
      xaxis: {
        lines: {
          show: true,
        },
      },
      yaxis: {
        lines: {
          show: true,
        },
      },
      padding: {
        top: 0,
        right: 0,
        bottom: 0,
        left: 15,
      },
    },
    legend: {
      position: "bottom",
      horizontalAlign: "center",
      markers: {
        radius: 12,
      },
      itemMargin: {
        horizontal: 10,
        vertical: 10,
      },
      labels: {
        colors: "#64748b",
      },
    },
    tooltip: {
      theme: "light",
      y: {
        formatter: function (val) {
          return val !== null ? formatCompactRupiah(val) : "Belum terjadi";
        },
      },
    },
  };

  costChart = new ApexCharts(elContainer, options);
  costChart.render();
}
