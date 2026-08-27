<?php
return [

    /*
    |--------------------------------------------------------------------------
    | Theme
    |--------------------------------------------------------------------------
    */

    'theme' => [

        'primary' => '#628891',

        'secondary' => '#2f7f8f',

        'accent' => '#4fb6c8',

        'text' => '#1e293b',

        'muted' => '#64748b',

        'border' => '#e2e8f0',

        'background' => '#ffffff',

        'hover' => '#f8fafc',

    ],

    /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
    */

    'pagination' => [

        'default' => 10,

        'options' => [
            10,
            25,
            50,
            100,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Container
    |--------------------------------------------------------------------------
    */

    'container' => [

        'class' =>
        'w-full overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm ring-1 ring-slate-100',

    ],

    /*
    |--------------------------------------------------------------------------
    | Toolbar
    |--------------------------------------------------------------------------
    */

    'toolbar' => [

        'class' =>
        'border-b border-slate-100 bg-white/80 backdrop-blur-md px-6 py-4.5 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between',

    ],

    /*
    |--------------------------------------------------------------------------
    | Search
    |--------------------------------------------------------------------------
    */

    'search' => [

        'wrapperClass' =>
        'relative w-full sm:w-[240px] focus-within:sm:w-[320px] transition-all duration-300 ease-out',

        'inputClass' =>
        'h-10 w-full rounded-xl border border-slate-200 bg-slate-50/50 pl-10 pr-10 text-sm text-[#1e293b] font-medium outline-none placeholder:text-slate-400 transition-all duration-200 focus:border-[#628891] focus:bg-white focus:ring-4 focus:ring-[#628891]/10 hover:border-slate-300 focus:border-2',

        'iconClass' =>
        'text-[#628891] absolute left-3.5 top-1/2 -translate-y-1/2 transition-colors duration-200 group-focus-within:text-[#1e293b]',

    ],

    /*
    |--------------------------------------------------------------------------
    | Buttons
    |--------------------------------------------------------------------------
    */

    'buttons' => [

        'base' =>
        'inline-flex h-10 items-center justify-center gap-2 rounded-xl border text-sm font-semibold transition-all duration-200 active:scale-[0.98] disabled:cursor-not-allowed disabled:opacity-50 disabled:pointer-events-none',

        'default' =>
        'border-slate-200 bg-white px-4 text-[#1e293b] shadow-sm hover:border-slate-300 hover:bg-slate-50',

        'primary' =>
        'border-[#1e293b] bg-[#1e293b] px-4 text-white shadow-sm hover:bg-[#628891] hover:border-[#628891] focus:ring-4 focus:ring-[#1e293b]/10',

        'danger' =>
        'border-red-100 bg-red-50/50 px-4 text-red-600 hover:bg-red-100/70 hover:border-red-200 focus:ring-4 focus:ring-red-500/10',

        'filter' =>
        'border-[#628891] bg-white pr-4 pl-3 text-[#628891] shadow-sm hover:bg-[#628891] hover:text-white focus:ring-4 focus:ring-[#628891]/10 rounded-[50px]',

        'export' =>
        'border-slate-200 bg-white px-4 text-[#1e293b] shadow-sm hover:border-[#628891] hover:bg-[#628891]/5',

        'column' =>
        'border-slate-200 bg-white px-4 text-[#1e293b] shadow-sm hover:border-[#628891] hover:bg-[#628891]/5',

    ],

    /*
    |--------------------------------------------------------------------------
    | Filter
    |--------------------------------------------------------------------------
    */

    'filter' => [

        'badge' =>
        'ml-1.5 inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-[#628891] px-1 text-[10px] font-bold text-white ring-2 ring-white',

        'modalOverlay' =>
        'fixed inset-0 z-50 bg-[#1e293b]/40 backdrop-blur-sm transition-opacity duration-300',

        'modal' =>
        'fixed left-1/2 top-1/2 z-50 w-full max-w-lg -translate-x-1/2 -translate-y-1/2 rounded-2xl border border-slate-200/80 bg-white shadow-2xl ring-1 ring-[#1e293b]/5 transition-all duration-300',

        'header' =>
        'flex items-center justify-between border-b border-slate-100 bg-white px-6 py-4.5',

        'body' =>
        'bg-white px-6 py-6 space-y-4 max-h-[60vh] overflow-y-auto',

        'footer' =>
        'flex items-center justify-end gap-3 border-t border-slate-100 bg-slate-50/50 px-6 py-4',

        'input' =>
        'h-10 w-full rounded-xl border border-slate-200 bg-white px-3.5 text-sm text-[#1e293b] placeholder:text-slate-400 outline-none transition focus:border-[#628891] focus:ring-4 focus:ring-[#628891]/10 hover:border-slate-300',

        'select' =>
        'h-10 w-full rounded-xl border border-slate-200 bg-white px-3.5 text-sm text-[#1e293b] outline-none transition focus:border-[#628891] focus:ring-4 focus:ring-[#628891]/10 hover:border-slate-300 cursor-pointer',

    ],

    /*
    |--------------------------------------------------------------------------
    | Table
    |--------------------------------------------------------------------------
    */

    'table' => [

        'class' =>
        'min-w-full border-collapse bg-white text-left',

    ],

    /*
    |--------------------------------------------------------------------------
    | Table Header
    |--------------------------------------------------------------------------
    */

    'thead' => [

        'class' =>
        'bg-[#628891] text-white select-none',

        'rowClass' =>
        'border-b border-[#628891]/20',

        'cellClass' =>
        'px-6 py-5 bg-[#628891] text-xs font-bold uppercase tracking-wider text-white/90 shadow-[inset_0_-1px_0_rgba(0,0,0,0.05)]',

    ],

    /*
    |--------------------------------------------------------------------------
    | Table Body
    |--------------------------------------------------------------------------
    */

    'tbody' => [

        'class' =>
        'divide-y divide-slate-100 bg-white',

    ],

    /*
    |--------------------------------------------------------------------------
    | Table Row
    |--------------------------------------------------------------------------
    */

    'row' => [

        'class' =>
        'group border-b border-slate-100 transition-colors duration-150 hover:bg-[#628891]/5',

    ],

    /*
    |--------------------------------------------------------------------------
    | Table Cell
    |--------------------------------------------------------------------------
    */

    'cell' => [

        'class' =>
        'px-6 py-3 text-sm text-[#1e293b]/90 font-medium align-middle whitespace-nowrap',

    ],

    /*
    |--------------------------------------------------------------------------
    | Selection
    |--------------------------------------------------------------------------
    */

    'selection' => [

        'checkboxClass' =>
        'h-4.5 w-4.5 cursor-pointer rounded-md border-slate-300 text-[#628891] focus:ring-[#628891]/20 transition-all duration-150 checked:bg-[#628891]',

    ],

    /*
    |--------------------------------------------------------------------------
    | Pagination UI
    |--------------------------------------------------------------------------
    */

    'pagination_ui' => [

        'class' =>
        'flex flex-col gap-4 border-t border-slate-200 bg-white px-5 py-4 sm:flex-row sm:items-center sm:justify-between select-none',

        'infoClass' =>
        'text-sm text-slate-500',

        'selectClass' =>
        'h-9 rounded-lg border border-slate-200 bg-white px-3 pr-5 text-sm text-[#628891] outline-none transition focus:border-[#628891] focus:ring-2 focus:ring-[#628891]/20',

        'buttonClass' =>
        'inline-flex h-9 min-w-9 items-center justify-center rounded-lg border border-slate-200 bg-white px-3 text-sm font-medium text-[#1e293b] transition hover:border-[#628891] hover:bg-[#628891]/10 disabled:cursor-not-allowed disabled:opacity-40',

        'activeClass' =>
        '!border-[#628891] !bg-[#628891] !text-white hover:!border-[#628891] hover:!bg-[#628891]',

        'disabledClass' =>
        'cursor-not-allowed opacity-40',

    ],

    /*
    |--------------------------------------------------------------------------
    | Export
    |--------------------------------------------------------------------------
    */

    'export' => [

        'button' =>
        'border-slate-200 bg-white px-3 text-[#1e293b] hover:border-[#628891] hover:bg-[#628891]/5',

        'scope' =>
        'rounded-xl border border-slate-200/60 bg-slate-50/50 p-2',

        'scopeItemHoverBackground' =>
        '#62889130',

        'disabledColor' =>
        '#999',
        'scopeItemColor' => '#000'

    ],

    /*
    |--------------------------------------------------------------------------
    | Column Manager
    |--------------------------------------------------------------------------
    */

    'columnManager' => [

        'button' =>
        'border-slate-200 bg-white px-3 text-sm font-medium text-[#1e293b] shadow-sm hover:border-[#628891] hover:bg-[#628891]/5',

        'menu' => '',

        'menuHeader' => '',

        'title' => '',

        'resetButton' => '',

        'list' => '',

        'item' => '',

        'itemActive' => '',

        'label' => '',

        'checkbox' => '',

    ],

    /*
    |--------------------------------------------------------------------------
    | Loading
    |--------------------------------------------------------------------------
    */

    'loading' => [

        'spinner' =>
        'h-6 w-6 animate-spin rounded-full border-2 border-slate-100 border-t-[#628891]',

        'text' =>
        'text-sm font-medium text-slate-400 tracking-wide',

    ],

    /*
    |--------------------------------------------------------------------------
    | Empty State
    |--------------------------------------------------------------------------
    */

    'empty' => [

        'icon' =>
        'flex h-14 w-14 items-center justify-center rounded-2xl bg-[#628891]/10 text-[#628891]',

        'iconClass' =>
        'text-[#628891] h-6 w-6',

        'text' =>
        'text-sm font-medium text-slate-400 mt-2',

        'action' =>
        'rounded-xl bg-[#1e293b] px-4 py-2 text-sm font-semibold text-white shadow-sm transition-all duration-200 hover:bg-[#628891] active:scale-[0.98]',

    ],

];
