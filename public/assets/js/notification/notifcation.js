function tostifyCustomClose(el) {
    const parent = el.closest('.toastify');
    const close = parent.querySelector('.toast-close');

    close.click();
  }

  window.addEventListener('load', () => {
    // Tostify
    (function() {
      let i = 0;
      const callToast = document.querySelector("#hs-new-toast");
      const toastMarkup1 = `
        <div class="max-w-xs relative bg-layer border border-layer-line rounded-xl shadow-lg" role="alert" tabindex="-1" aria-labelledby="hs-toast-avatar-label">
          <div class="flex gap-x-3 p-4">
            <img class="shrink-0 size-8 rounded-full" src="https://images.unsplash.com/photo-1568602471122-7832951cc4c5?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=facearea&facepad=2&w=300&h=300&q=80" alt="Avatar">
            <button onclick="tostifyCustomClose(this)" type="button" class="absolute top-3 end-3 inline-flex shrink-0 justify-center items-center size-5 rounded-lg text-layer-foreground opacity-50 hover:opacity-100 focus:outline-hidden focus:opacity-100" aria-label="Close">
              <span class="sr-only">Close</span>
              <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"></path><path d="m6 6 12 12"></path></svg>
            </button>
            <div class="grow pe-4">
              <h3 id="hs-toast-avatar-label" class="text-layer-foreground font-medium text-sm">
                <span class="font-semibold">James</span> mentioned you in a comment
              </h3>
              <div class="mt-1 text-sm text-muted-foreground-2">
                Nice work! Keep it up!
              </div>
              <div class="mt-3">
                <button type="button" class="text-primary decoration-2 hover:underline font-medium text-sm focus:outline-hidden focus:underline">
                  Mark as read
                </button>
              </div>
            </div>
          </div>
        </div>
      `;
      const toastMarkup2 = `
        <div class="flex gap-x-3 p-4">
          <p class="text-sm text-layer-foreground">Your email has been sent</p>
          <div class="ms-auto">
            <button onclick="tostifyCustomClose(this)" type="button" class="inline-flex shrink-0 justify-center items-center size-5 rounded-lg text-layer-foreground opacity-50 hover:opacity-100 focus:outline-hidden focus:opacity-100" aria-label="Close">
              <span class="sr-only">Close</span>
              <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"></path><path d="m6 6 12 12"></path></svg>
            </button>
          </div>
        </div>
      `;

      callToast.addEventListener("click", () => {
        Toastify({
          text: i % 3 ? toastMarkup1 : toastMarkup2,
          className: "hs-toastify-on:opacity-100 opacity-0 fixed -top-20 sm:-top-37.5 end-0 sm:end-5 z-90 transition-all duration-300 w-80 bg-layer text-sm text-layer-foreground border border-layer-line rounded-xl shadow-lg [&>.toast-close]:hidden",
          duration: 3000,
          close: true,
          escapeMarkup: false
        }).showToast();

        i++;
      });
    })();
  });