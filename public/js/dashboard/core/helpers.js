export function formatRupiah(number) {
  if (number === null || number === undefined || isNaN(number)) return "Rp 0";
  return new Intl.NumberFormat("id-ID", {
    style: "currency",
    currency: "IDR",
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
  }).format(number);
}

export function formatCurrency(number) {
  return formatRupiah(number);
}

export function formatPercent(number, decimals = 2) {
  if (number === null || number === undefined || isNaN(number)) return "0%";
  return `${Number(number).toFixed(decimals)}%`;
}

export function calculateDaysLeft(endDate) {
  if (!endDate) return 0;

  const end = new Date(endDate);
  const today = new Date();

  today.setHours(0, 0, 0, 0);
  end.setHours(0, 0, 0, 0);

  const diffTime = end.getTime() - today.getTime();
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

  return diffDays;
}

export function formatDateIndo(dateString) {
  if (!dateString) return "-";

  try {
    const date = new Date(dateString);
    return date.toLocaleDateString("id-ID", {
      day: "numeric",
      month: "long",
      year: "numeric",
    });
  } catch (e) {
    return dateString;
  }
}
