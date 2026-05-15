import { getState } from "../core/state.js";

const scheduleStatusMap = {
  Early: {
    label: "Early",
    desc: "Ahead of schedule",
    spiTextClass: "text-emerald-700",
    containerClass: "bg-white rounded-xl p-4 border border-emerald-100 shadow-sm flex flex-col justify-between relative overflow-hidden group",
    iconClass: "bg-emerald-100 text-emerald-600",
    bigIconClass: "fas fa-forward-fast text-emerald-500",
  },
  "On Time": {
    label: "On Time",
    desc: "On schedule",
    spiTextClass: "text-slate-700",
    containerClass: "bg-white rounded-xl p-4 border border-slate-100 shadow-sm flex flex-col justify-between relative overflow-hidden group",
    iconClass: "bg-slate-100 text-slate-500",
    bigIconClass: "fas fa-check-circle text-emerald-500",
  },
  "Slightly Delay": {
    label: "Slightly Delay",
    desc: "Minor delay detected",
    spiTextClass: "text-amber-700",
    containerClass: "bg-white rounded-xl p-4 border border-amber-100 shadow-sm flex flex-col justify-between relative overflow-hidden group",
    iconClass: "bg-amber-100 text-amber-600",
    bigIconClass: "fas fa-exclamation-triangle text-amber-500",
  },
  Delayed: {
    label: "Delayed",
    desc: "Significant delay",
    spiTextClass: "text-rose-900",
    containerClass: "bg-white rounded-xl p-4 border border-rose-100 shadow-sm flex flex-col justify-between relative overflow-hidden group",
    iconClass: "bg-rose-200 text-rose-700",
    bigIconClass: "fas fa-exclamation-triangle text-rose-600",
  },
};

const overallStatusMap = {
  Safe: {
    label: "Safe",
    desc: "Project is on track",
    overallIcon: "fa-check-circle",
    overallClass: "bg-emerald-600 rounded-xl p-4 shadow-lg text-white flex items-center gap-4 relative overflow-hidden transition-all group",
  },
  Warning: {
    label: "Warning",
    desc: "Attention needed",
    overallIcon: "fa-exclamation-triangle",
    overallClass: "bg-amber-500 rounded-xl p-4 shadow-lg text-white flex items-center gap-4 relative overflow-hidden transition-all group",
  },
  Critical: {
    label: "Critical",
    desc: "Immediate action required",
    overallIcon: "fa-radiation",
    overallClass: "bg-rose-600 rounded-xl p-4 shadow-lg text-white flex items-center gap-4 relative overflow-hidden transition-all group",
  }
};

function getOverallStyle(spiValue) {
  if (spiValue >= 0.95) return overallStatusMap.Safe;
  if (spiValue >= 0.85) return overallStatusMap.Warning;
  return overallStatusMap.Critical;
}

function getScheduleStyle(status) {
  return scheduleStatusMap[status] ?? scheduleStatusMap["On Time"];
}

function setElementClass(el, className) {
  if (!el) return;
  el.className = className;
}

export function renderStatusCards() {
  const { data } = getState();
  if (!data || !data.overview) return;

  const scheduleContainer = document.getElementById(
    "container-status-schedule",
  );
  const scheduleIconBg = document.getElementById("icon-bg-schedule");
  const scheduleBigIcon = document.getElementById("icon-big-schedule");
  const scheduleText = document.getElementById("text-status-schedule");
  const containerSpi = document.getElementById("container-spi");
  const valueSpi = document.getElementById("val-spi");

  const costText = document.getElementById("text-status-cost");
  const costCpi = document.getElementById("val-cpi");
  const containerCpi = document.getElementById("container-cpi");

  const overallContainer = document.getElementById("container-status-overall");
  const overallIconBg = document.getElementById("icon-bg-overall");
  const overallIcon = document.getElementById("icon-status-overall");
  const overallText = document.getElementById("text-status-overall");
  const overallDesc = document.getElementById("desc-status-overall");

  const status = data.overview.schedule_status || "On Time";
  const spiValue = Number(data.overview.spi_value ?? 1);
  const style = getScheduleStyle(status);

  setElementClass(scheduleContainer, style.containerClass);
  if (scheduleIconBg) {
    scheduleIconBg.className = `w-16 h-16 md:w-24 md:h-24 rounded-full flex items-center justify-center shrink-0 transition-all duration-500 ${style.iconClass}`;
  }
  
  if (scheduleBigIcon) {
    scheduleBigIcon.className = `${style.bigIconClass} opacity-100 transition-opacity duration-300`;
  }

  if (scheduleText) {
    scheduleText.textContent = style.label;
    scheduleText.className = `text-xl md:text-3xl font-bold leading-none mb-2 ${style.spiTextClass}`;
  }

  if (containerSpi) {
    containerSpi.classList.remove("opacity-0");
    containerSpi.classList.add("opacity-100");
  }

  if (valueSpi) {
    valueSpi.textContent = spiValue.toFixed(2);
    valueSpi.className = style.spiTextClass;
  }

  if (costText) {
    costText.textContent = "N/A";
  }
  if (costCpi) {
    costCpi.textContent = "--";
  }
  if (containerCpi) {
    containerCpi.classList.remove("opacity-0");
    containerCpi.classList.add("opacity-100");
  }

  const overallStyle = getOverallStyle(spiValue);

  if (overallContainer) {
    overallContainer.className = overallStyle.overallClass;
  }
  if (overallIconBg) {
    overallIconBg.className =
      "w-16 h-16 md:w-24 md:h-24 rounded-full bg-white/15 flex items-center justify-center shrink-0 backdrop-blur-sm border border-white/10 relative z-10 transition-all duration-500";
  }
  if (overallIcon) {
    overallIcon.className = `fas ${overallStyle.overallIcon} text-3xl md:text-4xl text-white`;
  }
  if (overallText) {
    overallText.textContent = overallStyle.label;
  }
  if (overallDesc) {
    overallDesc.textContent = overallStyle.desc;
  }
}
