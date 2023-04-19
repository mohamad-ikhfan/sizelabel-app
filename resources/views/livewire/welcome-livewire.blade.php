<div class="mt-16">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8">
        @foreach ($materials as $item)
            <div
                class="scale-100 p-6 bg-white from-gray-700/50 via-transparent rounded-lg shadow-md shadow-gray-500/20 flex motion-safe:hover:scale-[1.01] transition-all duration-250 focus:outline focus:outline-2 focus:outline-red-500">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900 capitalize">{{ $item->name }}</h2>
                    <h2 class="text-xl font-semibold text-gray-900 uppercase">{{ $item->code }}</h2>

                    <p class="text-gray-500 text-sm leading-relaxed font-bold">
                        STOCK : {{ $item->stocks()->latest()->first()->last_stock }}
                    </p>
                </div>
            </div>
        @endforeach
    </div>
</div>
