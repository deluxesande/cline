<div>
    @php
        use Illuminate\Support\Facades\Storage;
        use Illuminate\Support\Facades\Auth;
        use App\Models\Message;
    @endphp
    <div class="flex h-[93vh]">
        <div class="bg-gray-100 w-1/4 border-r-none">
            <div class="p-2">
                <section class="my-2">
                    <div class="center gap-1">
                        <span class="text-gray-800 font-semibold text-xs">
                             Messages
                        </span>
                        <i class="fa-regular fa-envelope"></i>
                    </div>
                </section>
                <label class="w-full relative">
                    <input wire:model.live="search"
                           class="pl-12 pr-4 py-2 w-full rounded-lg border-b border-gray-200 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                           type="text" placeholder="Search users">
                    <i class="fas btn btn-sm btn-circle fa-search absolute top-1/2 transform -translate-y-1/2 left-2 btn-primary"></i>
                </label>
            </div>
            <div class="overflow-y-auto users-chat-container h-[calc(100vh-10rem)]">
                @php
                    function getUnreadMessagesCount($senderId, $recipientId)
                    {
                        return Message::where('sender_id', $senderId)
                            ->where('recipient_id', $recipientId)
                            ->where('read_at', null)
                            ->count();
                    }
                @endphp

                @foreach($users as $user)
                    <div wire:click="selectRecipient({{ $user->id }})"
                         class="{{ $selectedRecipientId == $user->id ? 'bg-gray-200' : '' }} p-4 cursor-pointer hover:bg-gray-200 flex gap-2">
                        <div class="flex w-14 relative items-center">
                            <img src="{{ $user->profile_image }}"
                                 class="ring-1 w-10 object-cover h-10 ring-gray-400 rounded-full mr-1"
                                 alt="{{ $user->name }}">
                            <!-- getUnreadMessagesCount($senderId, $recipientId): -->
                            @if (getUnreadMessagesCount($user->id, Auth::id()) > 0)
                                <div
                                    class="bg-blue-500 text-white h-4 w-4 rounded-full center text-[10px] mb-1 absolute right-0 top-0">
                                    {{ getUnreadMessagesCount($user->id, Auth::id()) }}
                                </div>
                            @endif
                        </div>
                        <div class="w-1/2">
                            <!-- 10 characters of the user's name -->
                            <h3 class="font-normal text-sm text-gray-800">{{ substr($user->name, 0, 13) }}{{ strlen($user->name) > 15 ? '...' : '' }}</h3>
                            @php
                                // Get the last message sent or received by the user
                                    $lastMessage = $user->messages()->where(function ($query) {
                                        $query->where('sender_id', Auth::id())
                                            ->orWhere('recipient_id', Auth::id());
                                    })->latest()->first();
                                    //if the current user is not the one who received the message remove the message
                                    if ($lastMessage && $lastMessage->recipient_id != Auth::id()) {
                                        //if the user is not the one who sent the message remove the message
                                        if ($lastMessage->sender_id != Auth::id()) {
                                            $lastMessage = null;
                                        }
                                    }
                            @endphp
                            @if ($lastMessage)
                                <p class="text-xs text-gray-500 truncate">
                                    @if ($lastMessage->sender_id == Auth::id())
                                        <span class="font-semibold">You:</span>
                                    @else
                                        <!-- Only the first name of the user -->
                                        <span class="font-semibold">{{ explode(' ', $user->name)[0] }}:</span>
                                    @endif
                                    @if($lastMessage->body)
                                        {{ $lastMessage->body }}
                                    @else
                                        <span class="text-red-500">
                                                <i class="fa-solid fa-paperclip"></i> Attachment
                                        </span>
                                    @endif
                                </p>
                            @else
                                <p class="text-xs text-gray-500 truncate">No messages yet.</p>
                            @endif
                        </div>
                        <div class="flex time flex-col items-end">
                            <span class="text-[10px] text-gray-500">
                                @if ($lastMessage)
                                    @if(str_contains($lastMessage->time, 'minutes') || str_contains($lastMessage->time, 'hours') || str_contains($lastMessage->time, 'days'))
                                        {{ str_replace('minutes', 'min', str_replace('hours', 'hr', str_replace('days', 'day', $lastMessage->time))) }}
                                    @endif
                                @endif
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="flex flex-col justify-between w-3/4 bg-white shadow-lg rounded-lg h-full overflow-hidden">
            @if($selectedRecipient)
                <div class="bg-gray-100 py-[10px] px-3 flex items-center justify-between">
                    <div x-data="{ showModal: false }" class="flex items-center">
                        <img @click="showModal = true" src="{{ $selectedRecipient->profile_image }}"
                             class="w-10 h-10 object-cover ring-1 ring-gray-400 rounded-full mr-4"
                             alt="{{ $selectedRecipient->name }}">
                        <h3 class="font-normal text-gray-800">{{ $selectedRecipient->name }}</h3>

                        <!-- Modal -->
                        <div x-show="showModal" class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title"
                             role="dialog" aria-modal="true">
                            <div
                                class="flex items-end justify-center min-h-screen p-2 pb-20 text-center sm:block sm:p-0">
                                <!--
                                  Background overlay, show/hide based on modal state.
                                -->
                                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                                     aria-hidden="true"></div>

                                <!-- Modal Content -->
                                <div
                                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                    <div class="bg-white p-2">
                                        <!-- Content goes here -->
                                        <h1 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                            {{ $selectedRecipient->name }}'s Profile
                                        </h1>
                                        <div class="mt-2">
                                            <img class="rounded-lg" src="{{ $selectedRecipient->profile_image }}"
                                                 alt="{{ $selectedRecipient->name }}">
                                        </div>
                                    </div>
                                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                        <button @click="showModal = false" type="button"
                                                class="mt-3 w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                            Close
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="overflow-y-auto h-full" id="messagesContainer">

                    @if ($messages->count() == 0)
                        <div class="flex items-center flex-col justify-center h-full">
                            <i class="fa-regular fa-bell-slash text-4xl text-gray-500"></i>
                            <p class="text-gray-500">No messages yet.</p>
                        </div>
                    @else
                        <div class="p-6 space-y-4">
                            @foreach($messages as $message)
                                <div
                                    id="message_{{ $message->id }}"
                                    class="{{ $message->sender_id == Auth::id() ? 'text-right' : 'text-left' }} max-w-2/3 text-sm mx-auto message">
                                    @if($message->body)
                                        <div
                                            class="inline-block max-w-[400px] {{ $message->sender_id == Auth::id() ? 'bg-blue-500 text-white sent-message ' : 'bg-gray-200 text-gray-800 received-message ' }} px-1 py-1/4">
                                            {{ $message->body }}
                                        </div>
                                    @endif
                                    <br>
                                    <!-- Button to open the modal, hidden by default and triggered by JavaScript -->
                                    <button class="btn" id="openModalButton" style="display:none;">Open Modal</button>

                                    <!-- Dialog modal structure -->
                                    <dialog id="my_modal_1" class="modal">
                                        <div class="modal-box">
                                            <h3 class="font-bold text-lg">Attachment Preview</h3>
                                            <div id="modalContent" class="py-4">Press ESC key or click the button below
                                                to close
                                            </div>
                                            <div class="modal-action">
                                                <form method="dialog">
                                                    <button class="btn">Close</button>
                                                </form>
                                            </div>
                                        </div>
                                    </dialog>

                                    @if ($message->attachments->count() > 0)
                                        <div
                                            class="inline-block {{ $message->sender_id == Auth::id() ? 'bg-blue-500 text-white sent-message-attachment ' : 'bg-gray-200 text-gray-800 received-message-attachment' }}">
                                            <div class="flex max-w-[392px] flex-wrap gap-1 -mx-3">
                                                @foreach($message->attachments as $attachment)
                                                    <div class="min-w-[128px]">
                                                        <div
                                                            onclick="viewAttachment('{{ $attachment->type }}', '{{ Storage::url($attachment->path) }}')"
                                                            class="bg-gray-200 rounded-xl overflow-hidden relative">
                                                            @if (in_array($attachment->type, ['image/jpeg', 'image/png', 'image/gif']))
                                                                <img src="{{ Storage::url($attachment->path) }}"
                                                                     alt="Attachment"
                                                                     class="w-[128px] h-32 object-cover">
                                                            @elseif ($attachment->type == 'application/pdf')
                                                                <div
                                                                    class="flex items-center justify-center h-32 bg-red-200">
                                                                    <i class="fas fa-file-pdf text-4xl text-red-500"></i>
                                                                </div>
                                                            @elseif ($attachment->type == 'application/zip' || $attachment->type == 'application/x-rar-compressed')
                                                                <div
                                                                    class="flex items-center justify-center h-32 bg-green-200">
                                                                    <i class="fas fa-file-archive text-4xl text-green-500"></i>
                                                                </div>
                                                            @elseif (str_starts_with($attachment->type, 'video/'))
                                                                <div
                                                                    class="flex items-center justify-center h-32 bg-blue-200">
                                                                    <i class="fas fa-file-video text-4xl text-blue-500"></i>
                                                                </div>
                                                            @elseif (str_starts_with($attachment->type, 'audio/'))
                                                                <div
                                                                    class="flex items-center justify-center h-32 bg-yellow-200">
                                                                    <i class="fas fa-file-audio text-4xl text-yellow-500"></i>
                                                                </div>
                                                            @else
                                                                <div
                                                                    class="flex items-center justify-center h-32 bg-gray-300">
                                                                    <i class="fas fa-file text-4xl text-gray-500"></i>
                                                                </div>
                                                            @endif
                                                            <a href="{{ Storage::url($attachment->path) }}"
                                                               class="absolute top-0 btn btn-xs btn-circle btn-ghost right-0 mt-1 mr-1 bg-gray-500 text-white text-xs px-2 py-1">
                                                                <i class="fas fa-download"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                @endforeach
                                                <script>
                                                    function showModalWithContent(title, content) {
                                                        document.getElementById('modalContent').innerHTML = content;
                                                        document.getElementById('my_modal_1').showModal();
                                                        alert(title + content + 'success');
                                                    }

                                                    function viewAttachment(type, path) {
                                                        let content = '';
                                                        if (['image/jpeg', 'image/png', 'image/gif'].includes(type)) {
                                                            content = `<img src="${path}" alt="Image" style="max-width: 100%; height: auto;">`;
                                                        } else if (type === 'application/pdf') {
                                                            content = `<iframe src="${path}" style="width:100%; height:500px;" frameborder="0"></iframe>`;
                                                        } else {
                                                            content = '<p>Preview not supported.</p>';
                                                        }
                                                        showModalWithContent('Attachment Preview', content);
                                                    }
                                                </script>
                                            </div>
                                        </div>
                                    @endif
                                    <!-- if no message or attachment -->
                                    @if (!$message->body && $message->attachments->count() == 0)
                                        <div
                                            class="inline-block max-w-[400px] {{ $message->sender_id == Auth::id() ? 'bg-blue-500 text-white sent-message ' : 'bg-gray-200 text-gray-800 received-message ' }} px-1 py-1/4">
                                            <i class="fas fa-exclamation-circle"></i> This message has been deleted.
                                        </div>
                                    @endif
                                    <div class="text-[10px] mt-1 text-blue-500">
                                        {{ $message->time }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
                <div class="flex flex-col">
                    @if (count($attachments) > 0)
                        <div class="flex w-full bg-gray-100 p-2 pt-3">
                            @foreach($attachments as $attachment)
                                <div
                                    class="flex relative flex-col items-center justify-center w-16 h-16 bg-blue-200 rounded-md mr-2">
                                    @if (in_array($attachment->getMimeType(), ['image/jpeg', 'image/png']))
                                        <img src="{{ $attachment->temporaryUrl() }}" alt="Attachment"
                                             class="w-16 h-16 object-cover rounded-md">
                                    @elseif ($attachment->getMimeType() == 'image/svg+xml')
                                        <i class="fas fa-file-image text-4xl text-gray-500"></i>
                                    @elseif ($attachment->getMimeType() == 'application/zip')
                                        <i class="fas fa-file-archive text-4xl text-gray-500"></i>
                                    @else
                                        <i class="fas fa-file text-4xl text-gray-500"></i>
                                    @endif
                                    <!-- Displaying the first five characters of the file name -->
                                    <span class="text-xs mt-1">
                                    {{ substr($attachment->getClientOriginalName(), 0, 5) }}{{ strlen($attachment->getClientOriginalName()) > 5 ? '...' : '' }}
                                </span>
                                    <button wire:click="removeAttachment('{{ $attachment->getClientOriginalName() }}')"
                                            class="absolute btn-warning btn btn-xs btn-circle top-0 right-1 text-red-500 hover:text-red-700 -mt-2 -mr-3">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            @endforeach

                        </div>
                    @endif
                    <div class="text-red-500 bg-gray-100 text-xs px-4">
                        @error('attachments.*') <span class="error m-1">{{ $message }}</span> @enderror
                        @error('newMessage') <span class="error m-1">{{ $message }}</span> @enderror
                    </div>
                    <div class="bg-gray-100 px-2 py-4 relative flex items-center">
                        <label class="w-full">
                            <input id="messageInput" wire:model="newMessage" wire:keydown.enter="sendMessage"
                                   class="message-input"
                                   type="text" placeholder="Type your message...">
                        </label>
                        <!-- Hidden file input -->
                        <input wire:model.live="attachments" type="file" id="fileInput" multiple style="display: none;">
                        <!-- Clip icon for opening file dialog -->
                        <button
                            class="btn absolute left-3 btn-ghost ring-1 ring-primary text-primary top[50%] transform[-50%] btn-sm btn-circle ml-2"
                            onclick="document.getElementById('fileInput').click();">
                            <i class="fas fa-paperclip"></i>
                        </button>
                        <!-- Send message button -->
                        <button wire:click="sendMessage"
                                wire:loading.class.remove="btn-primary"
                                wire:loading.class="btn-ghost"
                                class="btn absolute right-3 btn-primary top[50%] transform[-50%] btn-sm btn-circle mr-2">
                            <i wire:target="sendMessage" wire:loading.class="hidden" class="fas fa-paper-plane"></i>
                            <span wire:loading wire:target="sendMessage"
                                  class="loading loading-spinner text-primary loading-sm"></span>
                        </button>
                    </div>
                </div>
            @else
                <div class="flex items-center flex-col justify-center h-full">
                    <i class="fa-solid fa-user-slash text-4xl text-gray-500"></i>
                    <p class="text-gray-500">No user selected.</p>
                </div>
            @endif
        </div>
    </div>
    @script
    <script>
        $wire.on('messagesLoaded', () => {
            setTimeout(() => {
                try {
                    let messagesContainer = document.getElementById('messagesContainer');
                    let messages = messagesContainer.getElementsByClassName('message');
                    let lastMessage = messages[messages.length - 1];

                    lastMessage.scrollIntoView({behavior: 'smooth'});
                } catch (Exception) {
                    //Ignore
                }
            }, 40);
        });
    </script>
    @endscript

</div>
