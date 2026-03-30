<div id="global-preloader" class="fixed inset-0 z-[9999] bg-white flex flex-col items-center justify-center transition-opacity duration-500 opacity-100">
    <!-- Icon Container -->
    <div class="relative flex items-center justify-center w-20 h-20">
        <!-- Outer ringing circle (Subtle) -->
        <div class="absolute inset-0 border-[3px] border-slate-100 rounded-full"></div>
        
        <!-- Rotating spinner (Primary color) -->
        <div class="absolute inset-0 border-[3px] border-primary border-t-transparent rounded-full animate-spin"></div>
        
        <!-- Center Theme Icon -->
        <i class="fa-solid fa-hard-hat text-3xl text-primary relative z-10 animate-pulse"></i>
    </div>
    
    <!-- Text Output -->
    <div class="mt-4 text-[10px] sm:text-xs font-bold tracking-[0.2em] text-slate-400 uppercase animate-pulse">
        Memuat...
    </div>
</div>

<script src="<?= base_url('assets/js/partials/preloader.js') ?>"></script>
