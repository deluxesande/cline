<div class="clients p-2 rounded-lg h-[80vh] overflow-y-auto w-full">
    <div class="label flex flex-col center mb-4">
        <label class="w-full relative">
            <input type="text" wire:model.live="search" class="w-full p-2 rounded-lg border border-gray-200" placeholder="Search clients...">
            <i class="fas fa-search absolute right-3 top-[50%] transform -translate-y-1/2 text-gray-400"></i>
        </label>
    </div>
        <!-- if we have no clients -->
        @if($clients->isEmpty())
            <div class="center w-full">
                <div class="text-gray-500 text-center">
                    <i class="fa-solid text-5xl fa-user-slash"></i>
                    <p class="text-lg">No clients found</p>
                    <span class="text-sm italic text-gray-500">Try typing a client name or email</span>
                </div>
            </div>
        @else
            <div class="grid gap-4 grid-cols-1 sm:grid-cols-2 md:grid-cols-2">
                @foreach($clients as $client)
                    <div class="client-card bg-white p-4 rounded-lg mb-4">
                        <div class="top grid items-center gap-2 grid-cols-4">
                            <div class="w-8 p-[1px] animate-scale rounded-full ring-1">
                                <img
                                    class="rounded-full w-full h-full object-cover" src="{{ $client->profile_picture }}" alt="{{ $client->name }} profile picture">
                            </div>
                            <div class="name col-span-3 overflow-x-clip">
                        <span class="text-gray-900 block font-medium">
                            {{ Str::limit($client->name, 13) }}
                        </span>
                                <span class="text-xs text-gray-700">
                            {{ $client->email }}
                        </span>
                            </div>
                        </div>
                        <div class="mid mt-2">
                            <div class="text-xs text-gray-700">
                                {{ $client->phone }}
                            </div>
                        </div>
                        <div class="bottom mt-2">
                            <div class="text-xs text-gray-700">
                                {{ $client->address }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
</div>
