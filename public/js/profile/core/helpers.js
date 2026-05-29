export function fallbackValue(value, emptyLabel = "-") {
    if (value === null || value === undefined) return emptyLabel;
    const text = String(value).trim();
    return text === "" ? emptyLabel : text;
}

export function formatDateIndo(dateValue) {
    if (!dateValue) return "-";

    const date = new Date(dateValue);
    if (Number.isNaN(date.getTime())) return fallbackValue(dateValue);

    return new Intl.DateTimeFormat("id-ID", {
        day: "2-digit",
        month: "long",
        year: "numeric",
    }).format(date);
}

export function formatJoinedAt(profile) {
    const date = formatDateIndo(profile?.tgl_daftar);
    const time = fallbackValue(profile?.jam_daftar, "");
    return [date, time].filter(Boolean).join(" ");
}

export function normalizeWebsiteUrl(website) {
    const value = fallbackValue(website, "");
    if (!value) return "#";
    if (/^https?:\/\//i.test(value)) return value;
    return `https://${value}`;
}

export function getStatusClasses(status) {
    const normalized = String(status || "").toLowerCase();

    if (normalized === "aktif") {
        return {
            badge: "bg-emerald-50 text-emerald-600",
            dot: "bg-emerald-500",
        };
    }

    return {
        badge: "bg-slate-100 text-slate-600",
        dot: "bg-slate-400",
    };
}
