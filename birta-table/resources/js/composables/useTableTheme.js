import { usePage } from "@inertiajs/vue3";

export function useTableTheme() {
    const page = usePage();

    return page.props.birtaTable ?? {};
}