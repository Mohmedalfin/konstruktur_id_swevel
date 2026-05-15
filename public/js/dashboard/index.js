import { fetchDashboardData } from "./core/data.js";
import { renderOverviewCards } from "./components/OverviewCards.js";
import { renderSummaryTable } from "./components/WorkSummaryTable.js";
import {
  renderProgressChart,
  renderCostChart,
} from "./components/DashboardCharts.js";
import { renderStatusCards } from "./components/StatusCards.js";
import { initCategoryDetailModal } from "./components/CategoryDetailModal.js";


function hideGlobalLoader() {
  if (window.GlobalLoader) {
    window.GlobalLoader.hide();
  } else {
    const loader = document.getElementById("global-page-loader");
    if (loader) {
      loader.classList.add("opacity-0");
      setTimeout(() => loader.classList.add("pointer-events-none"), 500);
    }
  }
}

async function initDashboard() {
  try {
    console.log("Memulai inisialisasi Dashboard...");

    await fetchDashboardData();

    renderOverviewCards();
    renderSummaryTable();
    renderProgressChart();
    renderCostChart();
    renderStatusCards();
    initCategoryDetailModal();

    console.log("Dashboard berhasil di-render!");
  } catch (error) {
    console.error("Gagal memuat Dashboard:", error);

    if (typeof Swal !== "undefined") {
      Swal.fire({
        icon: "error",
        title: "Gagal Memuat Data",
        text: error.message || "Terjadi kesalahan saat menghubungi server.",
        confirmButtonColor: "#3085d6",
      });
    }
  } finally {
    hideGlobalLoader();
  }
}

document.addEventListener("DOMContentLoaded", () => {
  initDashboard();
});