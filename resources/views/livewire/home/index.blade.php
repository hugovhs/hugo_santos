<section aria-labelledby="titulo-inicio" class="mx-auto max-w-7xl px-4 py-8 sm:px-8 sm:py-12 lg:py-16">
    <div class="grid overflow-hidden rounded-3xl bg-[#f2f5eb] lg:grid-cols-2">
        <div class="flex flex-col items-start justify-center px-6 py-10 sm:px-12 sm:py-14 lg:p-14 xl:p-16">
            <h1 id="titulo-inicio" class="max-w-lg text-4xl leading-[1.08] font-bold tracking-tight text-[#112e3e] sm:text-5xl xl:text-6xl">
                Nutrición para <span class="text-green-800">tu día a día</span>
            </h1>
            <p class="mt-5 max-w-sm text-base leading-relaxed text-slate-700 sm:text-lg">
                Encuentra suplementos y productos de bienestar para acompañar tu rutina.
            </p>
            <button type="button" disabled
                class="mt-8 inline-flex items-center gap-3 rounded-full bg-green-800 px-6 py-3.5 text-sm font-semibold text-white">
                Ver productos
                <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
            </button>
        </div>

        <img src="{{ asset('images/nutriviva-hero.webp') }}" width="1280" height="960" alt=""
            fetchpriority="high" class="aspect-[4/3] h-full w-full object-cover sm:aspect-[16/9] lg:aspect-[4/3]">
    </div>
</section>
