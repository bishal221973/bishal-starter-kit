<script setup>
import {
    ref,
    computed,
    onMounted,
    onUnmounted,
} from "vue";

import { router } from "@inertiajs/vue3";

const props = defineProps({
    enabled: {
        type: Boolean,
        default: false,
    },

    /*
    |--------------------------------------------------------------------------
    | Inactivity time in minutes
    |--------------------------------------------------------------------------
    */
    timeout: {
        type: Number,
        default: 30,
    },

    /*
    |--------------------------------------------------------------------------
    | Show warning before logout
    |--------------------------------------------------------------------------
    */
    showWarning: {
        type: Boolean,
        default: true,
    },

    /*
    |--------------------------------------------------------------------------
    | Warning duration in minutes
    |--------------------------------------------------------------------------
    */
    warningTime: {
        type: Number,
        default: 1,
    },
});

const warningVisible = ref(false);
const remainingSeconds = ref(0);

let inactivityTimer = null;
let warningTimer = null;
let countdownTimer = null;

let isLoggingOut = false;

/*
|--------------------------------------------------------------------------
| Computed
|--------------------------------------------------------------------------
*/

const formattedRemaining = computed(() => {
    const minutes = Math.floor(
        remainingSeconds.value / 60
    );

    const seconds =
        remainingSeconds.value % 60;

    return `${String(minutes).padStart(2, "0")}:${String(
        seconds
    ).padStart(2, "0")}`;
});

/*
|--------------------------------------------------------------------------
| Clear inactivity timer
|--------------------------------------------------------------------------
*/

const clearInactivityTimer = () => {
    if (inactivityTimer) {
        clearTimeout(inactivityTimer);
        inactivityTimer = null;
    }
};

/*
|--------------------------------------------------------------------------
| Clear warning timer
|--------------------------------------------------------------------------
*/

const clearWarningTimer = () => {
    if (warningTimer) {
        clearTimeout(warningTimer);
        warningTimer = null;
    }
};

/*
|--------------------------------------------------------------------------
| Clear countdown
|--------------------------------------------------------------------------
*/

const clearCountdownTimer = () => {
    if (countdownTimer) {
        clearInterval(countdownTimer);
        countdownTimer = null;
    }
};

/*
|--------------------------------------------------------------------------
| Clear all timers
|--------------------------------------------------------------------------
*/

const clearAllTimers = () => {
    clearInactivityTimer();
    clearWarningTimer();
    clearCountdownTimer();
};

/*
|--------------------------------------------------------------------------
| Logout
|--------------------------------------------------------------------------
*/

const logout = () => {
    if (isLoggingOut) {
        return;
    }

    isLoggingOut = true;

    clearAllTimers();

    router.post(
        route("logout"),
        {},
        {
            preserveScroll: false,
            onFinish: () => {
                isLoggingOut = false;
            },
        }
    );
};

/*
|--------------------------------------------------------------------------
| Start Warning Countdown
|--------------------------------------------------------------------------
*/

const startWarning = () => {
    warningVisible.value = true;

    const warningSeconds = Math.max(
        Number(props.warningTime) || 1,
        1
    ) * 60;

    remainingSeconds.value = warningSeconds;

    clearWarningTimer();
    clearCountdownTimer();

    /*
    |--------------------------------------------------------------------------
    | Countdown
    |--------------------------------------------------------------------------
    */

    countdownTimer = setInterval(() => {
        if (remainingSeconds.value > 0) {
            remainingSeconds.value--;
        }

        if (remainingSeconds.value <= 0) {
            clearCountdownTimer();
        }
    }, 1000);

    /*
    |--------------------------------------------------------------------------
    | Logout after warning
    |--------------------------------------------------------------------------
    */

    warningTimer = setTimeout(() => {
        logout();
    }, warningSeconds * 1000);
};

/*
|--------------------------------------------------------------------------
| Start Inactivity Timer
|--------------------------------------------------------------------------
*/

const startInactivityTimer = () => {
    if (!props.enabled || isLoggingOut) {
        return;
    }

    clearInactivityTimer();

    const timeoutMinutes = Math.max(
        Number(props.timeout) || 30,
        1
    );

    inactivityTimer = setTimeout(() => {
        /*
        |--------------------------------------------------------------------------
        | Show warning
        |--------------------------------------------------------------------------
        */

        if (props.showWarning) {
            startWarning();
        } else {
            logout();
        }
    }, timeoutMinutes * 60 * 1000);
};

/*
|--------------------------------------------------------------------------
| User Activity
|--------------------------------------------------------------------------
*/

const handleActivity = () => {
    if (!props.enabled || isLoggingOut) {
        return;
    }

    /*
    |--------------------------------------------------------------------------
    | If warning is visible, user became active again.
    |--------------------------------------------------------------------------
    */

    if (warningVisible.value) {
        warningVisible.value = false;

        clearWarningTimer();
        clearCountdownTimer();

        remainingSeconds.value = 0;
    }

    /*
    |--------------------------------------------------------------------------
    | Restart inactivity timer
    |--------------------------------------------------------------------------
    */

    startInactivityTimer();
};

/*
|--------------------------------------------------------------------------
| Events
|--------------------------------------------------------------------------
*/

const activityEvents = [
    "mousemove",
    "mousedown",
    "keydown",
    "touchstart",
    "touchmove",
    "scroll",
    "wheel",
    "click",
];

/*
|--------------------------------------------------------------------------
| Mount
|--------------------------------------------------------------------------
*/

onMounted(() => {
    if (!props.enabled) {
        return;
    }

    activityEvents.forEach((event) => {
        window.addEventListener(
            event,
            handleActivity,
            {
                passive: true,
            }
        );
    });

    startInactivityTimer();
});

/*
|--------------------------------------------------------------------------
| Unmount
|--------------------------------------------------------------------------
*/

onUnmounted(() => {
    activityEvents.forEach((event) => {
        window.removeEventListener(
            event,
            handleActivity
        );
    });

    clearAllTimers();
});
</script>

<template>
    <!-- =========================================================
         AUTO LOGOUT WARNING
    ========================================================== -->

    <Teleport to="body">
        <Transition name="logout-warning">
            <div
                v-if="warningVisible"
                class="
                    fixed
                    inset-0
                    z-[999999]
                    flex
                    items-center
                    justify-center
                    bg-black/50
                    px-5
                    backdrop-blur-sm
                "
            >
                <div
                    class="
                        w-full
                        max-w-md
                        overflow-hidden
                        rounded-3xl
                        border
                        border-white/10
                        bg-white
                        shadow-2xl
                    "
                >
                    <!-- Header -->

                    <div
                        class="
                            flex
                            items-center
                            gap-4
                            border-b
                            border-gray-100
                            px-6
                            py-5
                        "
                    >
                        <div
                            class="
                                flex
                                h-12
                                w-12
                                shrink-0
                                items-center
                                justify-center
                                rounded-2xl
                                bg-amber-100
                                text-amber-600
                            "
                        >
                            <i
                                class="
                                    fas
                                    fa-user-clock
                                    text-lg
                                "
                            ></i>
                        </div>

                        <div>
                            <h3
                                class="
                                    text-lg
                                    font-bold
                                    text-gray-900
                                "
                            >
                                Are you still there?
                            </h3>

                            <p
                                class="
                                    mt-0.5
                                    text-sm
                                    text-gray-500
                                "
                            >
                                You have been inactive.
                            </p>
                        </div>
                    </div>

                    <!-- Body -->

                    <div class="px-6 py-6 text-center">
                        <div
                            class="
                                mx-auto
                                flex
                                h-24
                                w-24
                                items-center
                                justify-center
                                rounded-full
                                bg-gray-100
                            "
                        >
                            <span
                                class="
                                    text-2xl
                                    font-bold
                                    tabular-nums
                                    text-gray-900
                                "
                            >
                                {{ formattedRemaining }}
                            </span>
                        </div>

                        <p
                            class="
                                mt-5
                                text-sm
                                leading-6
                                text-gray-500
                            "
                        >
                            You will be automatically logged out
                            because of inactivity.
                        </p>
                    </div>

                    <!-- Footer -->

                    <div
                        class="
                            flex
                            items-center
                            justify-end
                            gap-3
                            border-t
                            border-gray-100
                            bg-gray-50
                            px-6
                            py-4
                        "
                    >
                        <button
                            type="button"
                            class="
                                rounded-xl
                                border
                                border-gray-200
                                bg-white
                                px-4
                                py-2.5
                                text-sm
                                font-medium
                                text-gray-700
                                transition
                                hover:bg-gray-100
                            "
                            @click="logout"
                        >
                            Logout
                        </button>

                        <button
                            type="button"
                            class="
                                rounded-xl
                                bg-[#348797]
                                px-5
                                py-2.5
                                text-sm
                                font-semibold
                                text-white
                                shadow-sm
                                transition
                                hover:brightness-110
                            "
                            @click="handleActivity"
                        >
                            Stay Signed In
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<style scoped>
.logout-warning-enter-active,
.logout-warning-leave-active {
    transition:
        opacity 0.25s ease,
        transform 0.25s ease;
}

.logout-warning-enter-from,
.logout-warning-leave-to {
    opacity: 0;
}

.logout-warning-enter-from > div,
.logout-warning-leave-to > div {
    transform: scale(0.95) translateY(10px);
}

.logout-warning-enter-active > div,
.logout-warning-leave-active > div {
    transition: transform 0.25s ease;
}
</style>