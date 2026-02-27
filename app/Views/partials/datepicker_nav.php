<?php
// wajib kasih $id unik, misal: "tanggal_mulai" / "estimasi_selesai"
$id = $id ?? 'datepicker';
$placeholder = $placeholder ?? 'Pilih tanggal';
?>

<div class="flex rounded-lg" data-datepicker-wrap>
    <input
        id="<?= esc($id) ?>"
        class="hs-datepicker py-3 px-4 block relative focus:z-1 w-full bg-white border border-gray-300 rounded-s-md text-sm text-slate-800 placeholder:text-slate-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none"
        type="text"
        placeholder="<?= esc($placeholder) ?>"
        readonly
        data-hs-datepicker='{
      "applyUtilityClasses": true,
      "type": "default",
      "selectedDates": ["today"],
      "dateMax": "2050-12-31",
      "mode": "custom-select",
      "dateFormat": "D MMMM YYYY, dddd",
      "replaceTodayWithText": true,
      "templates": {
        "arrowPrev": "<button data-vc-arrow=\"prev\"><svg class=\"shrink-0 size-4\" xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><path d=\"m15 18-6-6 6-6\"></path></svg></button>",
        "arrowNext": "<button data-vc-arrow=\"next\"><svg class=\"shrink-0 size-4\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><path d=\"m9 18 6-6-6-6\"></path></svg></button>"
      }
    }'>

    <button type="button"
        data-datepicker-today
        class="relative focus:z-1 -ms-px py-3 px-4 inline-flex justify-center items-center bg-white border border-gray-300 font-medium text-slate-700 hover:bg-slate-100 transition-all text-sm">
        Today
    </button>

    <button type="button"
        data-datepicker-prev
        class="relative focus:z-1 -ms-px py-3 px-4 inline-flex justify-center items-center bg-white border border-gray-300 font-medium text-slate-700 hover:bg-slate-100 transition-all text-sm">
        <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="m15 18-6-6 6-6"></path>
        </svg>
    </button>

    <button type="button"
        data-datepicker-next
        class="relative focus:z-1 -ms-px py-3 px-4 inline-flex justify-center items-center rounded-e-md bg-white border border-gray-300 font-medium text-slate-700 hover:bg-slate-100 transition-all text-sm">
        <svg class="shrink-0 size-4" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="m9 18 6-6-6-6"></path>
        </svg>
    </button>
</div>