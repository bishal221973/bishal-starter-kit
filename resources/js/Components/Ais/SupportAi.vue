<script setup>
import { ref, nextTick } from 'vue';

const isOpen = ref(false);
const message = ref('');
const loading = ref(false);

const messages = ref([
    {
        role: 'assistant',
        content:
            "Hi! 👋 I'm your Starter Kit support assistant. How can I help you today?",
    },
]);

const messagesContainer = ref(null);

/*
|--------------------------------------------------------------------------
| Scroll to bottom
|--------------------------------------------------------------------------
*/
const scrollToBottom = async () => {
    await nextTick();

    if (messagesContainer.value) {
        messagesContainer.value.scrollTop =
            messagesContainer.value.scrollHeight;
    }
};

/*
|--------------------------------------------------------------------------
| Toggle chat
|--------------------------------------------------------------------------
*/
const toggleChat = () => {
    isOpen.value = !isOpen.value;

    if (isOpen.value) {
        scrollToBottom();
    }
};

/*
|--------------------------------------------------------------------------
| Close chat
|--------------------------------------------------------------------------
*/
const closeChat = () => {
    isOpen.value = false;
};

/*
|--------------------------------------------------------------------------
| Clear chat
|--------------------------------------------------------------------------
*/
const clearChat = () => {
    if (loading.value) {
        return;
    }

    messages.value = [
        {
            role: 'assistant',
            content:
                "Hi! 👋 I'm your Starter Kit support assistant. How can I help you today?",
        },
    ];
};

/*
|--------------------------------------------------------------------------
| Send message
|--------------------------------------------------------------------------
*/
const sendMessage = async () => {
    if (!message.value.trim() || loading.value) {
        return;
    }

    const userMessage = message.value.trim();

    /*
    |--------------------------------------------------------------------------
    | Add user message
    |--------------------------------------------------------------------------
    */
    messages.value.push({
        role: 'user',
        content: userMessage,
    });

    /*
    |--------------------------------------------------------------------------
    | Clear input
    |--------------------------------------------------------------------------
    */
    message.value = '';

    /*
    |--------------------------------------------------------------------------
    | Create empty assistant message
    |--------------------------------------------------------------------------
    |
    | We will update this message as tokens arrive.
    |
    */
    const assistantMessage = {
        role: 'assistant',
        content: '',
    };

    messages.value.push(assistantMessage);

    loading.value = true;

    await scrollToBottom();

    try {
        /*
        |--------------------------------------------------------------------------
        | CSRF token
        |--------------------------------------------------------------------------
        */
        const csrfToken = document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute('content');

        /*
        |--------------------------------------------------------------------------
        | Request Laravel
        |--------------------------------------------------------------------------
        */
        const response = await fetch(
            route('support.chat'),
            {
                method: 'POST',

                headers: {
                    'Content-Type':
                        'application/json',

                    Accept:
                        'text/event-stream',

                    ...(csrfToken && {
                        'X-CSRF-TOKEN':
                            csrfToken,
                    }),
                },

                body: JSON.stringify({
                    message: userMessage,
                }),
            }
        );

        /*
        |--------------------------------------------------------------------------
        | HTTP error
        |--------------------------------------------------------------------------
        */
        if (!response.ok) {
            let errorMessage =
                'Something went wrong.';

            try {
                const error =
                    await response.json();

                errorMessage =
                    error.message ||
                    error.errors?.message?.[0] ||
                    errorMessage;
            } catch {
                //
            }

            throw new Error(errorMessage);
        }

        /*
        |--------------------------------------------------------------------------
        | Browser streaming support
        |--------------------------------------------------------------------------
        */
        if (!response.body) {
            throw new Error(
                'Streaming is not supported by this browser.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Create stream reader
        |--------------------------------------------------------------------------
        */
        const reader =
            response.body.getReader();

        const decoder =
            new TextDecoder();

        let buffer = '';

        /*
        |--------------------------------------------------------------------------
        | Read stream
        |--------------------------------------------------------------------------
        */
        while (true) {
            const {
                value,
                done,
            } = await reader.read();

            /*
            |--------------------------------------------------------------------------
            | Stream finished
            |--------------------------------------------------------------------------
            */
            if (done) {
                break;
            }

            /*
            |--------------------------------------------------------------------------
            | Decode chunk
            |--------------------------------------------------------------------------
            */
            buffer += decoder.decode(
                value,
                {
                    stream: true,
                }
            );

            /*
            |--------------------------------------------------------------------------
            | SSE events
            |--------------------------------------------------------------------------
            |
            | Laravel sends events separated by:
            |
            | \n\n
            |
            */
            const events =
                buffer.split('\n\n');

            /*
            |--------------------------------------------------------------------------
            | Keep incomplete event
            |--------------------------------------------------------------------------
            */
            buffer =
                events.pop() || '';

            /*
            |--------------------------------------------------------------------------
            | Process each SSE event
            |--------------------------------------------------------------------------
            */
            for (const event of events) {
                const lines =
                    event.split('\n');

                for (const line of lines) {
                    /*
                    |--------------------------------------------------------------------------
                    | Only process data:
                    |--------------------------------------------------------------------------
                    */
                    if (
                        !line.startsWith(
                            'data:'
                        )
                    ) {
                        continue;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Extract data
                    |--------------------------------------------------------------------------
                    */
                    const data =
                        line
                            .substring(5)
                            .trim();

                    if (!data) {
                        continue;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | OpenAI/Laravel stream end
                    |--------------------------------------------------------------------------
                    */
                    if (
                        data === '[DONE]'
                    ) {
                        continue;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Parse JSON
                    |--------------------------------------------------------------------------
                    */
                    let parsed;

                    try {
                        parsed =
                            JSON.parse(data);
                    } catch (error) {
                        console.log(
                            'Non JSON stream:',
                            data
                        );

                        continue;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Debug
                    |--------------------------------------------------------------------------
                    */
                    console.log(
                        'Laravel AI Stream:',
                        parsed
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | TEXT DELTA
                    |--------------------------------------------------------------------------
                    |
                    | Laravel AI returns:
                    |
                    | {
                    |     type: "text_delta",
                    |     delta: "Hello"
                    | }
                    |
                    */
                    if (
                        parsed.type ===
                        'text_delta'
                    ) {
                        assistantMessage.content +=
                            parsed.delta || '';

                        await scrollToBottom();

                        continue;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Error
                    |--------------------------------------------------------------------------
                    */
                    if (
                        parsed.type ===
                        'error'
                    ) {
                        assistantMessage.content =
                            parsed.message ||
                            'AI response failed.';

                        continue;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Stream end
                    |--------------------------------------------------------------------------
                    */
                    if (
                        parsed.type ===
                        'stream_end'
                    ) {
                        console.log(
                            'AI stream completed'
                        );

                        continue;
                    }
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Flush decoder
        |--------------------------------------------------------------------------
        */
        buffer += decoder.decode();

        /*
        |--------------------------------------------------------------------------
        | Process remaining buffer
        |--------------------------------------------------------------------------
        */
        if (buffer.trim()) {
            const events =
                buffer.split('\n\n');

            for (const event of events) {
                const lines =
                    event.split('\n');

                for (const line of lines) {
                    if (
                        !line.startsWith(
                            'data:'
                        )
                    ) {
                        continue;
                    }

                    const data =
                        line
                            .substring(5)
                            .trim();

                    if (
                        !data ||
                        data === '[DONE]'
                    ) {
                        continue;
                    }

                    try {
                        const parsed =
                            JSON.parse(data);

                        if (
                            parsed.type ===
                            'text_delta'
                        ) {
                            assistantMessage.content +=
                                parsed.delta || '';
                        }
                    } catch {
                        //
                    }
                }
            }
        }
    } catch (error) {
        console.error(
            'Support chat error:',
            error
        );

        assistantMessage.content =
            error.message ||
            'Sorry, something went wrong. Please try again.';
    } finally {
        loading.value = false;

        await scrollToBottom();
    }
};

/*
|--------------------------------------------------------------------------
| Keyboard
|--------------------------------------------------------------------------
*/
const handleKeydown = (event) => {
    /*
     * Enter = send
     * Shift + Enter = new line
     */
    if (
        event.key === 'Enter' &&
        !event.shiftKey
    ) {
        event.preventDefault();

        sendMessage();
    }
};
</script>

<template>

    <!-- ========================================================= -->
    <!-- Floating Button -->
    <!-- ========================================================= -->

    <button
        type="button"
        @click="toggleChat"
        class="fixed bottom-6 right-6 z-[9999] flex h-14 w-14 items-center justify-center rounded-full bg-slate-900 text-white shadow-xl transition-all duration-300 hover:scale-105 hover:bg-slate-800 focus:outline-none"
        aria-label="Open support chat"
    >
        <i
            v-if="!isOpen"
            class="fa fa-message text-lg"
        ></i>

        <i
            v-else
            class="fa fa-xmark text-xl"
        ></i>
    </button>


    <!-- ========================================================= -->
    <!-- Chat Window -->
    <!-- ========================================================= -->

    <Transition
        enter-active-class="transition duration-200 ease-out"
        enter-from-class="translate-y-4 scale-95 opacity-0"
        enter-to-class="translate-y-0 scale-100 opacity-100"
        leave-active-class="transition duration-150 ease-in"
        leave-from-class="translate-y-0 scale-100 opacity-100"
        leave-to-class="translate-y-4 scale-95 opacity-0"
    >

        <div
            v-if="isOpen"
            class="fixed bottom-24 right-6 z-[9999] flex h-[600px] w-[390px] max-w-[calc(100vw-2rem)] flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl"
        >

            <!-- ================================================= -->
            <!-- Header -->
            <!-- ================================================= -->

            <div
                class="flex shrink-0 items-center justify-between bg-slate-900 px-5 py-4 text-white"
            >

                <div
                    class="flex items-center gap-3"
                >

                    <!-- Bot -->
                    <div
                        class="relative flex h-10 w-10 items-center justify-center rounded-full bg-white/10"
                    >
                        <i
                            class="fa fa-robot text-lg"
                        ></i>

                        <span
                            class="absolute bottom-0 right-0 h-3 w-3 rounded-full border-2 border-slate-900 bg-green-400"
                        ></span>
                    </div>

                    <!-- Title -->
                    <div>
                        <h3
                            class="text-sm font-semibold"
                        >
                            Starter Kit Support
                        </h3>

                        <p
                            class="mt-0.5 text-xs text-slate-300"
                        >
                            AI Developer Assistant
                        </p>
                    </div>

                </div>


                <!-- Actions -->
                <div
                    class="flex items-center gap-1"
                >

                    <!-- Clear -->
                    <button
                        type="button"
                        @click="clearChat"
                        :disabled="loading"
                        title="Clear conversation"
                        class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-300 transition hover:bg-white/10 hover:text-white disabled:cursor-not-allowed disabled:opacity-40"
                    >
                        <i
                            class="fa fa-trash-can text-xs"
                        ></i>
                    </button>

                    <!-- Close -->
                    <button
                        type="button"
                        @click="closeChat"
                        title="Close"
                        class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-300 transition hover:bg-white/10 hover:text-white"
                    >
                        <i
                            class="fa fa-xmark text-sm"
                        ></i>
                    </button>

                </div>

            </div>


            <!-- ================================================= -->
            <!-- Messages -->
            <!-- ================================================= -->

            <div
                ref="messagesContainer"
                class="flex-1 overflow-y-auto bg-slate-50 p-4"
            >

                <div class="space-y-4">

                    <!-- Messages -->

                    <div
                        v-for="(
                            item, index
                        ) in messages"
                        :key="index"
                        class="flex"
                        :class="
                            item.role ===
                            'user'
                                ? 'justify-end'
                                : 'justify-start'
                        "
                    >

                        <!-- Assistant avatar -->

                        <div
                            v-if="
                                item.role ===
                                'assistant'
                            "
                            class="mr-2 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-slate-900 text-xs text-white"
                        >
                            <i
                                class="fa fa-robot"
                            ></i>
                        </div>


                        <!-- Message bubble -->

                        <div
                            class="max-w-[80%] rounded-2xl px-4 py-3 text-sm leading-relaxed"
                            :class="
                                item.role ===
                                'user'
                                    ? 'rounded-br-md bg-slate-900 text-white'
                                    : 'rounded-bl-md border border-slate-200 bg-white text-slate-700 shadow-sm'
                            "
                        >

                            <div
                                class="whitespace-pre-wrap break-words"
                            >
                                {{ item.content }}
                            </div>


                            <!-- Streaming cursor -->

                            <span
                                v-if="
                                    loading &&
                                    item.role ===
                                        'assistant' &&
                                    index ===
                                        messages.length -
                                            1
                                "
                                class="ml-0.5 inline-block h-4 w-[2px] animate-pulse bg-slate-500 align-middle"
                            ></span>

                        </div>

                    </div>


                    <!-- ================================================= -->
                    <!-- Typing indicator -->
                    <!-- ================================================= -->

                    <div
                        v-if="
                            loading &&
                            messages[
                                messages.length -
                                    1
                            ]?.role ===
                                'user'
                        "
                        class="flex items-center gap-2"
                    >

                        <div
                            class="flex h-8 w-8 items-center justify-center rounded-full bg-slate-900 text-xs text-white"
                        >
                            <i
                                class="fa fa-robot"
                            ></i>
                        </div>

                        <div
                            class="rounded-2xl rounded-bl-md border border-slate-200 bg-white px-4 py-3 shadow-sm"
                        >

                            <div
                                class="flex gap-1"
                            >

                                <span
                                    class="h-2 w-2 animate-bounce rounded-full bg-slate-400"
                                ></span>

                                <span
                                    class="h-2 w-2 animate-bounce rounded-full bg-slate-400 [animation-delay:150ms]"
                                ></span>

                                <span
                                    class="h-2 w-2 animate-bounce rounded-full bg-slate-400 [animation-delay:300ms]"
                                ></span>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            <!-- ================================================= -->
            <!-- Input -->
            <!-- ================================================= -->

            <div
                class="shrink-0 border-t border-slate-200 bg-white p-3"
            >

                <form
                    @submit.prevent="sendMessage"
                >

                    <div
                        class="flex items-end gap-2 rounded-xl border border-slate-200 bg-slate-50 p-1.5 transition focus-within:border-slate-400"
                    >

                        <!-- Textarea -->

                        <textarea
                            v-model="message"
                            @keydown="handleKeydown"
                            :disabled="loading"
                            rows="1"
                            placeholder="Ask about the starter kit..."
                            class="max-h-24 min-h-[40px] flex-1 resize-none border-0 bg-transparent px-3 py-2.5 text-sm text-slate-700 outline-none placeholder:text-slate-400 focus:ring-0 disabled:opacity-50"
                        ></textarea>


                        <!-- Send -->

                        <button
                            type="submit"
                            :disabled="
                                loading ||
                                !message.trim()
                            "
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-slate-900 text-white transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-40"
                        >

                            <i
                                v-if="!loading"
                                class="fa fa-paper-plane text-xs"
                            ></i>

                            <i
                                v-else
                                class="fa fa-spinner animate-spin text-xs"
                            ></i>

                        </button>

                    </div>

                </form>


                <!-- Footer -->

                <p
                    class="mt-2 text-center text-[10px] text-slate-400"
                >
                    AI support for your Laravel Starter Kit
                </p>

            </div>

        </div>

    </Transition>

</template>