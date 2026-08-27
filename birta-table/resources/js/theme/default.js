export default {
    table: {
        wrapper:
            "overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm",

        table:
            "w-full text-sm",

        header: {
            wrapper:
                "bg-slate-50",

            row:
                "border-b border-slate-200",

            cell:
                "px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500",
        },

        body: {
            row:
                "border-b border-slate-100 transition hover:bg-slate-50",

            cell:
                "px-4 py-3 text-slate-700",
        },
    },

    toolbar: {
        wrapper:
            "flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 p-4",

        search:
            "h-10 rounded-lg border border-slate-300 px-3 text-sm",

        button:
            "rounded-lg border border-slate-300 px-3 py-2 text-sm",
    },

    pagination: {
        wrapper:
            "flex items-center justify-between border-t border-slate-200 px-4 py-3",

        info:
            "text-sm text-slate-500",

        select:
            "h-9 rounded-lg border border-slate-300 px-2 text-sm",

        button:
            "rounded-lg border border-slate-300 px-3 py-1.5 text-sm",

        active:
            "bg-[#628891] text-white",

        disabled:
            "cursor-not-allowed opacity-50",
    },

    filter: {
        overlay:
            "bg-black/40",

        modal:
            "h-screen w-full max-w-sm rounded-l-2xl bg-white shadow-2xl",

        header:
            "border-b p-5",

        body:
            "p-5",

        footer:
            "border-t p-5",

        input:
            "h-10 w-full rounded-lg border border-gray-300 px-3",

        select:
            "h-10 w-full rounded-lg border border-gray-300 px-3",

        applyButton:
            "rounded-lg bg-[#628891] px-4 py-2 text-sm font-medium text-white",

        cancelButton:
            "rounded-lg border px-4 py-2",

        clearButton:
            "text-sm text-red-500",
    },
};