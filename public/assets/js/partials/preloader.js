(function() {
        const preloader = document.getElementById("global-preloader");
        if (!preloader) return;

        // --- 1. HIDE PRELOADER ON LOAD ---
        window.addEventListener("load", () => {
            // Memberi jeda super singkat agar perenderan halaman 'settled'
            setTimeout(() => {
                preloader.classList.remove("opacity-100");
                preloader.classList.add("opacity-0");
                
                // Hapus dari layar sepenuhnya setelah transisi selesai (500ms dari tailwind transition)
                setTimeout(() => {
                    preloader.style.display = "none";
                }, 500);
            }, 100);
        });

        // --- 2. SHOW PRELOADER ON NAVIGATION ---
        document.addEventListener("DOMContentLoaded", () => {
            
            // Tangkap semua klik pada link (a href)
            document.querySelectorAll('a').forEach(link => {
                link.addEventListener('click', function (e) {
                    const targetUrl = this.getAttribute('href');
                    const targetAttr = this.getAttribute('target');
                    const isDropdownInfo = this.closest('.hs-dropdown-menu'); // preline dropdown bypass jika bukan link url nyata
                    
                    // Bypass kondisi link yang tidak menyebabkan perpindahan halaman (Anchors, Href kosong, Tab Baru, JS, Email)
                    if (
                        !targetUrl || 
                        targetUrl === '#' || 
                        targetUrl.startsWith('javascript:') || 
                        targetUrl.startsWith('mailto:') || 
                        this.hasAttribute('download') || 
                        targetAttr === '_blank'
                    ) {
                        return;
                    }
                    
                    // Tambahan bypass link hash internal 
                    if(targetUrl.includes('#') && !targetUrl.startsWith('http') && !targetUrl.startsWith('/')) {
                       return; 
                    }

                    // Tampilkan preloader sebelum layar memuat ulang!
                    preloader.style.display = "flex";
                    preloader.offsetHeight; // Reflow browser
                    preloader.classList.remove("opacity-0");
                    preloader.classList.add("opacity-100");
                });
            });

            // --- 3. SHOW PRELOADER ON FORMS SUBMIT ---
            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', function (e) {
                    // Bypass target=_blank atau validasi gagal
                    if (this.getAttribute('target') === '_blank') return;
                    if (this.checkValidity && !this.checkValidity()) return;

                    preloader.style.display = "flex";
                    preloader.offsetHeight;
                    preloader.classList.remove("opacity-0");
                    preloader.classList.add("opacity-100");
                });
            });

            // --- 4. SAFETEY NET (Browser Back/Forward Button Cache) ---
            window.addEventListener('pageshow', function (event) {
                if (event.persisted) { // Kalau di-load lewat cache History (Back Button)
                    preloader.classList.remove("opacity-100");
                    preloader.classList.add("opacity-0");
                    preloader.style.display = "none";
                }
            });
        });
    })();