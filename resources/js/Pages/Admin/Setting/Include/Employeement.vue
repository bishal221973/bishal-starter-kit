<script setup>
const props = defineProps({
    user: {
        type: Object,
        required: true,
    },
});

const info = props.user?.info ?? {};

const employeeTypeLabels = {
    full_time: "Full Time",
    part_time: "Part Time",
    contract: "Contract",
    temporary: "Temporary",
    intern: "Intern",
};

const salaryTypeLabels = {
    monthly: "Monthly",
    yearly: "Yearly",
    hourly: "Hourly",
    daily: "Daily",
};

const employeeType = employeeTypeLabels[info.employee_type] ?? info.employee_type ?? "—";
const salaryType = salaryTypeLabels[info.salary_type] ?? info.salary_type ?? "—";

const formatDate = (date) => {
    if (!date) return "—";

    const parsed = new Date(date);

    if (isNaN(parsed.getTime())) {
        return date;
    }

    return parsed.toLocaleDateString("en-US", {
        year: "numeric",
        month: "short",
        day: "numeric",
    });
};

const formatSalary = (salary) => {
    if (!salary) return "—";

    return new Intl.NumberFormat("en-IN", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    }).format(salary);
};
</script>

<template>
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

        <!-- Header -->
        <div
            class="border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white px-6 py-5"
        >
            <div class="flex items-center gap-4">
                <div
                    class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-primary/10 text-primary"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M20 7l-8-4-8 4m16 0v10l-8 4-8-4V7m16 0l-8 4m0 0L4 7m8 4v10"
                        />
                    </svg>
                </div>

                <div>
                    <h2 class="text-base font-semibold text-slate-900">
                        Employment Information
                    </h2>

                    <p class="mt-0.5 text-sm text-slate-500">
                        Your position, employment and salary information.
                    </p>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="p-6">

            <!-- Employee Code -->
            <div
                class="mb-6 rounded-xl border border-slate-200 bg-slate-50 p-4"
            >
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-500">
                            Employee Code
                        </p>

                        <p class="mt-1 font-mono text-lg font-semibold text-slate-900">
                            {{ info.employee_code || "—" }}
                        </p>
                    </div>

                    <span
                        class="rounded-lg bg-white px-3 py-1.5 text-xs font-medium text-slate-600 shadow-sm ring-1 ring-slate-200"
                    >
                        Employee ID
                    </span>
                </div>
            </div>

            <!-- Employment Details -->
            <div>
                <h3 class="mb-4 text-sm font-semibold text-slate-900">
                    Employment Details
                </h3>

                <div class="grid grid-cols-1 gap-x-8 gap-y-5 md:grid-cols-2">

                    <!-- Employee Type -->
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-500">
                            Employee Type
                        </p>

                        <p class="mt-1 text-sm font-medium text-slate-900">
                            {{ employeeType }}
                        </p>
                    </div>

                    <!-- Department -->
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-500">
                            Department
                        </p>

                        <p class="mt-1 text-sm font-medium text-slate-900">
                            {{ info.department || "—" }}
                        </p>
                    </div>

                    <!-- Designation -->
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-500">
                            Designation
                        </p>

                        <p class="mt-1 text-sm font-medium text-slate-900">
                            {{ info.designation || "—" }}
                        </p>
                    </div>

                    <!-- Joined Date -->
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-500">
                            Joined Date
                        </p>

                        <p class="mt-1 text-sm font-medium text-slate-900">
                            {{ formatDate(info.joined_at) }}
                        </p>
                    </div>

                    <!-- Probation -->
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-500">
                            Probation Ends
                        </p>

                        <p class="mt-1 text-sm font-medium text-slate-900">
                            {{ formatDate(info.probation_ends_at) }}
                        </p>
                    </div>

                    <!-- Employment Ends -->
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-500">
                            Employment Ends
                        </p>

                        <p class="mt-1 text-sm font-medium text-slate-900">
                            {{ formatDate(info.employment_ends_at) }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Divider -->
            <div class="my-7 border-t border-slate-100"></div>

            <!-- Salary -->
            <div>
                <h3 class="mb-4 text-sm font-semibold text-slate-900">
                    Compensation
                </h3>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">

                    <!-- Salary -->
                    <div
                        class="rounded-xl border border-slate-200 bg-white p-4"
                    >
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-500">
                            Salary
                        </p>

                        <div class="mt-2 flex items-baseline gap-2">
                            <span class="text-xl font-semibold text-slate-900">
                                {{ formatSalary(info.salary) }}
                            </span>

                            <span class="text-sm text-slate-500">
                                NPR
                            </span>
                        </div>
                    </div>

                    <!-- Salary Type -->
                    <div
                        class="rounded-xl border border-slate-200 bg-white p-4"
                    >
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-500">
                            Salary Type
                        </p>

                        <p class="mt-2 text-base font-semibold text-slate-900">
                            {{ salaryType }}
                        </p>
                    </div>

                </div>
            </div>

        </div>
    </div>
</template>