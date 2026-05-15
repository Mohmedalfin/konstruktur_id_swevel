export function initPhotoLightbox() {
    if (document.getElementById('photo-lightbox')) return; // already initialised

    const overlay = document.createElement('div');
    overlay.id = 'photo-lightbox';
    overlay.style.cssText = `
        display: none;
        position: fixed;
        inset: 0;
        z-index: 9999;
        background: rgba(0,0,0,0.88);
        backdrop-filter: blur(6px);
        align-items: center;
        justify-content: center;
        flex-direction: column;
        gap: 16px;
        transition: opacity 0.2s ease;
        opacity: 0;
    `;

    overlay.innerHTML = `
        <!-- Close Button -->
        <button id="lightbox-close" style="
            position: absolute;
            top: 20px;
            right: 20px;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.25);
            color: white;
            font-size: 18px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.15s;
            z-index: 2;
        " onmouseenter="this.style.background='rgba(255,255,255,0.3)'" onmouseleave="this.style.background='rgba(255,255,255,0.15)'">
            <i class="fas fa-times"></i>
        </button>

        <!-- Image Wrapper -->
        <div style="max-width: 90vw; max-height: 82vh; display: flex; flex-direction: column; align-items: center; gap: 12px;">
            <img id="lightbox-img" src="" alt="Foto Dokumentasi" style="
                max-width: 90vw;
                max-height: 78vh;
                object-fit: contain;
                border-radius: 12px;
                box-shadow: 0 25px 60px rgba(0,0,0,0.6);
                border: 2px solid rgba(255,255,255,0.1);
            ">
            <a id="lightbox-link" href="#" target="_blank" style="
                display: inline-flex;
                align-items: center;
                gap: 6px;
                font-size: 12px;
                color: rgba(255,255,255,0.55);
                text-decoration: none;
                transition: color 0.15s;
            " onmouseenter="this.style.color='rgba(255,255,255,0.9)'" onmouseleave="this.style.color='rgba(255,255,255,0.55)'">
                <i class="fas fa-external-link-alt" style="font-size:10px;"></i> Buka di tab baru
            </a>
        </div>
    `;

    document.body.appendChild(overlay);

    const closeLightbox = () => {
        overlay.style.opacity = '0';
        setTimeout(() => {
            overlay.style.display = 'none';
        }, 180);
    };

    overlay.querySelector('#lightbox-close').addEventListener('click', closeLightbox);

    overlay.addEventListener('click', (e) => {
        if (e.target === overlay) closeLightbox();
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && overlay.style.display === 'flex') closeLightbox();
    });

    window._openPhotoLightbox = (url) => {
        const img  = overlay.querySelector('#lightbox-img');
        const link = overlay.querySelector('#lightbox-link');

        img.src   = url;
        link.href = url;

        overlay.style.display = 'flex';
        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                overlay.style.opacity = '1';
            });
        });
    };
}
