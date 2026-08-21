<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from "vue";
import { usePage } from "@inertiajs/vue3";

const page = usePage();

const props = defineProps({
  enabled: {
    type: Boolean,
    default: false,
  },

  timeout: {
    type: Number,
    default: 300,
  },

  type: {
    type: String,
    default: "image",
    validator: (value) => ["image", "slider", "video"].includes(value),
  },

  images: {
    type: Array,
    default: () => [],
  },

  video: {
    type: String,
    default: "",
  },

  showClock: {
    type: Boolean,
    default: true,
  },

  showDate: {
    type: Boolean,
    default: true,
  },

  sliderInterval: {
    type: Number,
    default: 5000,
  },

  /*
    |--------------------------------------------------------------------------
    | User
    |--------------------------------------------------------------------------
    */

  user: {
    type: Object,
    default: null,
  },
});

/*
|--------------------------------------------------------------------------
| STATE
|--------------------------------------------------------------------------
*/

const visible = ref(false);
const currentImage = ref(0);
const currentTime = ref(new Date());
const controlsVisible = ref(false);

let inactivityTimer = null;
let clockTimer = null;
let sliderTimer = null;

/*
|--------------------------------------------------------------------------
| USER
|--------------------------------------------------------------------------
*/

const authUser = computed(() => {
  return props.user || page.props.auth?.user || page.props.user || {};
});

const userName = computed(() => {
  return authUser.value?.name || "User";
});

const userEmail = computed(() => {
  return authUser.value?.email || "";
});

const userAvatar = computed(() => {
  return (
    authUser.value?.profile_photo_url ||
    authUser.value?.avatar ||
    authUser.value?.profile_photo ||
    null
  );
});

const initials = computed(() => {
  const name = userName.value.trim();

  if (!name) {
    return "U";
  }

  return name
    .split(" ")
    .slice(0, 2)
    .map((word) => word.charAt(0))
    .join("")
    .toUpperCase();
});

/*
|--------------------------------------------------------------------------
| COMPUTED
|--------------------------------------------------------------------------
*/

const formattedTime = computed(() => {
  return currentTime.value.toLocaleTimeString([], {
    hour: "2-digit",
    minute: "2-digit",
    second: "2-digit",
  });
});

const formattedDate = computed(() => {
  return currentTime.value.toLocaleDateString([], {
    weekday: "long",
    year: "numeric",
    month: "long",
    day: "numeric",
  });
});

const hasImages = computed(() => {
  return Array.isArray(props.images) && props.images.length > 0;
});

const currentBackground = computed(() => {
  if (!hasImages.value) {
    return null;
  }

  return "/storage/" + props.images[currentImage.value] || props.images[0];
});

/*
|--------------------------------------------------------------------------
| TIMER
|--------------------------------------------------------------------------
*/

const clearInactivityTimer = () => {
  if (inactivityTimer) {
    clearTimeout(inactivityTimer);
    inactivityTimer = null;
  }
};

const startInactivityTimer = () => {
  clearInactivityTimer();

  if (!props.enabled || visible.value) {
    return;
  }

  const timeout = Math.max(Number(props.timeout) || 300, 1);

  inactivityTimer = setTimeout(() => {
    showScreenSaver();
  }, timeout * 1000);
};

/*
|--------------------------------------------------------------------------
| ACTIVITY
|--------------------------------------------------------------------------
*/

// const handleActivity = () => {
//     if (visible.value) {
//         controlsVisible.value = true;

//         hideScreenSaver();

//         return;
//     }

//     startInactivityTimer();
// };

// const handleActivity = () => {
//   if (visible.value) {
//     controlsVisible.value = true;
//     return;
//   }

//   startInactivityTimer();
// };

const handleActivity = () => {
    if (visible.value) {
        controlsVisible.value = true;
        return;
    }

    startInactivityTimer();
};

const handleKeydown = (event) => {
    if (!visible.value) {
        return;
    }

    hideScreenSaver();
};

const handleMouseMove = () => {
  if (!visible.value) {
    return;
  }

  controlsVisible.value = true;
};

const hideControls = () => {
  controlsVisible.value = false;
};

/*
|--------------------------------------------------------------------------
| SHOW
|--------------------------------------------------------------------------
*/

const showScreenSaver = () => {
  if (!props.enabled) {
    return;
  }

  visible.value = true;
  controlsVisible.value = false;
  currentImage.value = 0;

  clearInactivityTimer();

  startSlider();
};

/*
|--------------------------------------------------------------------------
| HIDE
|--------------------------------------------------------------------------
*/

const hideScreenSaver = () => {
  visible.value = false;

  controlsVisible.value = false;

  stopSlider();

  currentImage.value = 0;

  startInactivityTimer();
};

/*
|--------------------------------------------------------------------------
| SLIDER
|--------------------------------------------------------------------------
*/

const startSlider = () => {
  stopSlider();

  if (props.type !== "slider" || !hasImages.value || props.images.length <= 1) {
    return;
  }

  const interval = Math.max(Number(props.sliderInterval) || 5000, 1000);

  sliderTimer = setInterval(() => {
    if (!visible.value) {
      return;
    }

    currentImage.value = (currentImage.value + 1) % props.images.length;
  }, interval);
};

const stopSlider = () => {
  if (sliderTimer) {
    clearInterval(sliderTimer);
    sliderTimer = null;
  }
};

/*
|--------------------------------------------------------------------------
| IMAGE CONTROLS
|--------------------------------------------------------------------------
*/

const nextImage = () => {
  if (!hasImages.value) {
    return;
  }

  currentImage.value = (currentImage.value + 1) % props.images.length;
};

const previousImage = () => {
  if (!hasImages.value) {
    return;
  }

  currentImage.value =
    (currentImage.value - 1 + props.images.length) % props.images.length;
};

/*
|--------------------------------------------------------------------------
| WATCHERS
|--------------------------------------------------------------------------
*/

watch(
  () => props.enabled,
  (enabled) => {
    if (!enabled) {
      visible.value = false;

      clearInactivityTimer();
      stopSlider();

      return;
    }

    startInactivityTimer();
  }
);

watch(
  () => props.type,
  () => {
    currentImage.value = 0;

    if (visible.value) {
      startSlider();
    }
  }
);

watch(
  () => props.images,
  () => {
    if (!props.images?.length) {
      currentImage.value = 0;

      stopSlider();

      return;
    }

    if (currentImage.value >= props.images.length) {
      currentImage.value = 0;
    }

    if (visible.value) {
      startSlider();
    }
  },
  {
    deep: true,
  }
);

/*
|--------------------------------------------------------------------------
| EVENTS
|--------------------------------------------------------------------------
*/

const activityEvents = [
  "mousemove",
  "mousedown",
  "touchstart",
  "scroll",
  "wheel",
];

/*
|--------------------------------------------------------------------------
| MOUNT
|--------------------------------------------------------------------------
*/

onMounted(() => {
  activityEvents.forEach((event) => {
    window.addEventListener(event, handleActivity, {
      passive: true,
    });
  });

  clockTimer = setInterval(() => {
    currentTime.value = new Date();
  }, 1000);

  startInactivityTimer();
});

/*
|--------------------------------------------------------------------------
| UNMOUNT
|--------------------------------------------------------------------------
*/

onUnmounted(() => {
  activityEvents.forEach((event) => {
    window.removeEventListener(event, handleActivity);
  });

  clearInactivityTimer();

  stopSlider();

  if (clockTimer) {
    clearInterval(clockTimer);
    clockTimer = null;
  }
});

onMounted(() => {
    activityEvents.forEach((event) => {
        window.addEventListener(
            event,
            handleActivity,
            { passive: true }
        );
    });

    window.addEventListener("keydown", handleKeydown);

    clockTimer = setInterval(() => {
        currentTime.value = new Date();
    }, 1000);

    startInactivityTimer();
});

onUnmounted(() => {
    activityEvents.forEach((event) => {
        window.removeEventListener(
            event,
            handleActivity
        );
    });

    window.removeEventListener(
        "keydown",
        handleKeydown
    );

    clearInactivityTimer();
    stopSlider();

    if (clockTimer) {
        clearInterval(clockTimer);
        clockTimer = null;
    }
});
</script>

<template>
  <Teleport to="body">
    <Transition name="screensaver" appear>
      <div
        v-if="visible"
        class="screen-lock"
        @click="hideScreenSaver"
        @keydown="hideScreenSaver"
        @mousemove="handleMouseMove"
      >
        <!-- =====================================================
                     BACKGROUND
                ====================================================== -->

        <div class="absolute inset-0 overflow-hidden">
          <!-- IMAGE -->

          <Transition
            v-if="type === 'image' && hasImages"
            name="background"
            mode="out-in"
          >
            <img
              :key="currentBackground"
              :src="currentBackground"
              alt=""
              class="background-image"
              draggable="false"
            />
          </Transition>

          <!-- SLIDER -->

          <Transition
            v-else-if="type === 'slider' && hasImages"
            name="background"
            mode="out-in"
          >
            <img
              :key="currentBackground"
              :src="currentBackground"
              alt=""
              class="background-image"
              draggable="false"
            />
          </Transition>

          <!-- VIDEO -->

          <video
            v-else-if="type === 'video' && video"
            :src="video"
            autoplay
            muted
            loop
            playsinline
            class="background-image"
          />

          <!-- FALLBACK -->

          <div v-else class="absolute inset-0 fallback-background"></div>

          <!-- BLUR -->

          <div class="background-blur"></div>

          <!-- GRADIENT -->

          <div class="background-gradient"></div>

          <!-- VIGNETTE -->

          <div class="background-vignette"></div>
        </div>

        <!-- =====================================================
                     TOP BAR
                ====================================================== -->

        <div
          class="absolute top-0 left-0 right-0 z-30 flex items-center justify-between px-6 py-5 sm:px-10"
          :class="{
            'ui-hidden': !controlsVisible,
          }"
        >
          <!-- Brand -->

          <div class="flex items-center gap-3">
            <div
              class="flex h-10 w-10 items-center justify-center rounded-xl border border-white/20 bg-white/10 backdrop-blur-xl"
            >
              <i class="fas fa-lock text-sm text-white"></i>
            </div>

            <div class="hidden sm:block">
              <div class="text-sm font-semibold text-white">Screen Saver</div>

              <div class="text-xs text-white/50">Your session is protected</div>
            </div>
          </div>

          <!-- Security -->

          <div
            class="flex items-center gap-2 rounded-full border border-white/10 bg-black/20 px-4 py-2 text-xs text-white/70 backdrop-blur-xl"
          >
            <span
              class="h-2 w-2 rounded-full bg-emerald-400 shadow-[0_0_12px_rgba(52,211,153,.8)]"
            ></span>

            Active
          </div>
        </div>

        <!-- =====================================================
                     CENTER
                ====================================================== -->

        <div
          class="relative z-20 flex min-h-screen flex-col items-center justify-center px-5 text-center"
        >
          <!-- CLOCK -->

          <div v-if="showClock" class="lock-clock">
            {{ formattedTime }}
          </div>

          <!-- DATE -->

          <div v-if="showDate" class="lock-date">
            {{ formattedDate }}
          </div>

          <!-- USER CARD -->

          <div class="mt-8 user-card">
            <!-- Avatar -->

            <div class="avatar-wrapper">
              <img
                v-if="userAvatar"
                :src="userAvatar"
                :alt="userName"
                class="avatar-image"
              />

              <div v-else class="avatar-fallback">
                {{ initials }}
              </div>

              <!-- Online -->

              <span class="online-indicator">
                <span></span>
              </span>
            </div>

            <!-- Name -->

            <div class="mt-4 text-xl font-semibold tracking-tight text-white sm:text-2xl">
              {{ userName }}
            </div>

            <!-- Email -->

            <div v-if="userEmail" class="mt-1 text-sm text-white/55">
              {{ userEmail }}
            </div>

            <!-- Unlock -->

            <button type="button" class="unlock-button" @click.stop="hideScreenSaver">
              <i class="fas fa-unlock-alt"></i>

              Continue
            </button>
          </div>
        </div>

        <!-- =====================================================
                     SLIDER CONTROLS
                ====================================================== -->

        <template v-if="type === 'slider' && hasImages && images.length > 1">
          <button
            type="button"
            class="slider-button left-5 sm:left-8"
            :class="{
              'ui-hidden': !controlsVisible,
            }"
            @click.stop="previousImage"
          >
            <i class="fas fa-chevron-left"></i>
          </button>

          <button
            type="button"
            class="slider-button right-5 sm:right-8"
            :class="{
              'ui-hidden': !controlsVisible,
            }"
            @click.stop="nextImage"
          >
            <i class="fas fa-chevron-right"></i>
          </button>

          <!-- Dots -->

          <div
            class="absolute bottom-20 left-1/2 z-30 flex -translate-x-1/2 items-center gap-2"
            :class="{
              'ui-hidden': !controlsVisible,
            }"
          >
            <button
              v-for="(_, index) in images"
              :key="index"
              type="button"
              class="slider-dot"
              :class="{
                active: currentImage === index,
              }"
              @click.stop="currentImage = index"
            ></button>
          </div>
        </template>

        <!-- =====================================================
                     BOTTOM
                ====================================================== -->

        <div
          class="absolute bottom-6 left-1/2 z-30 -translate-x-1/2"
          :class="{
            'ui-hidden': !controlsVisible,
          }"
        >
          <div
            class="flex items-center gap-2 whitespace-nowrap rounded-full border border-white/10 bg-black/20 px-4 py-2.5 text-xs text-white/60 backdrop-blur-xl"
          >
            <i class="fas fa-mouse-pointer text-[10px]"></i>

            Move your mouse or press any key to continue
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<style scoped>
/*
|--------------------------------------------------------------------------
| SCREEN LOCK
|--------------------------------------------------------------------------
*/

.screen-lock {
  position: fixed;
  inset: 0;
  z-index: 99999;
  overflow: hidden;
  background: #111;
  color: white;
  user-select: none;
}

/*
|--------------------------------------------------------------------------
| BACKGROUND
|--------------------------------------------------------------------------
*/

.background-image {
  position: absolute;
  inset: -2%;
  width: 104%;
  height: 104%;
  object-fit: cover;

  filter: saturate(1.05) contrast(1.03);

  animation: backgroundZoom 20s ease-in-out infinite alternate;
}

.background-blur {
  position: absolute;
  inset: 0;

  backdrop-filter: blur(2px);
}

.background-gradient {
  position: absolute;
  inset: 0;

  background: linear-gradient(
    180deg,
    rgba(0, 0, 0, 0.45) 0%,
    rgba(0, 0, 0, 0.08) 35%,
    rgba(0, 0, 0, 0.18) 55%,
    rgba(0, 0, 0, 0.72) 100%
  );
}

.background-vignette {
  position: absolute;
  inset: 0;

  background: radial-gradient(
    ellipse at center,
    transparent 20%,
    rgba(0, 0, 0, 0.28) 100%
  );
}

.fallback-background {
  background: radial-gradient(
      circle at 20% 20%,
      rgba(52, 135, 151, 0.55),
      transparent 35%
    ),
    radial-gradient(circle at 80% 80%, rgba(30, 64, 175, 0.55), transparent 40%),
    linear-gradient(135deg, #111827, #0f172a);
}

/*
|--------------------------------------------------------------------------
| CLOCK
|--------------------------------------------------------------------------
*/

.lock-clock {
  font-size: clamp(4rem, 10vw, 9rem);

  line-height: 0.9;

  font-weight: 200;

  letter-spacing: -0.04em;

  color: white;

  text-shadow: 0 8px 40px rgba(0, 0, 0, 0.45);

  animation: clockEnter 1s cubic-bezier(0.16, 1, 0.3, 1);
}

.lock-date {
  margin-top: 22px;

  font-size: clamp(0.95rem, 2vw, 1.3rem);

  font-weight: 400;

  letter-spacing: 0.04em;

  color: rgba(255, 255, 255, 0.78);

  text-shadow: 0 3px 15px rgba(0, 0, 0, 0.5);

  animation: dateEnter 1.2s ease;
}

/*
|--------------------------------------------------------------------------
| USER CARD
|--------------------------------------------------------------------------
*/

.user-card {
  min-width: 280px;

  display: flex;
  flex-direction: column;
  align-items: center;

  padding: 22px 28px 26px;

  border: 1px solid rgba(255, 255, 255, 0.13);

  border-radius: 24px;

  background: rgba(15, 23, 42, 0.2);

  backdrop-filter: blur(25px);

  box-shadow: 0 25px 70px rgba(0, 0, 0, 0.25), inset 0 1px 0 rgba(255, 255, 255, 0.08);

  animation: cardEnter 1s cubic-bezier(0.16, 1, 0.3, 1);
}

/*
|--------------------------------------------------------------------------
| AVATAR
|--------------------------------------------------------------------------
*/

.avatar-wrapper {
  position: relative;

  width: 88px;
  height: 88px;
}

.avatar-image,
.avatar-fallback {
  width: 88px;
  height: 88px;

  border-radius: 9999px;

  border: 3px solid rgba(255, 255, 255, 0.65);

  box-shadow: 0 8px 35px rgba(0, 0, 0, 0.4);
}

.avatar-image {
  object-fit: cover;
}

.avatar-fallback {
  display: flex;
  align-items: center;
  justify-content: center;

  background: linear-gradient(135deg, var(--primary), #1e6473);

  color: white;

  font-size: 28px;
  font-weight: 600;
}

/*
|--------------------------------------------------------------------------
| ONLINE
|--------------------------------------------------------------------------
*/

.online-indicator {
  position: absolute;

  right: 2px;
  bottom: 3px;

  width: 20px;
  height: 20px;

  display: flex;
  align-items: center;
  justify-content: center;

  border: 3px solid rgba(20, 20, 20, 0.8);

  border-radius: 9999px;

  background: #22c55e;
}

.online-indicator span {
  width: 6px;
  height: 6px;

  border-radius: 9999px;

  background: white;

  opacity: 0.8;
}

/*
|--------------------------------------------------------------------------
| CONTINUE BUTTON
|--------------------------------------------------------------------------
*/

.unlock-button {
  margin-top: 20px;

  display: flex;
  align-items: center;
  justify-content: center;
  gap: 9px;

  min-width: 150px;

  padding: 10px 18px;

  border: 1px solid rgba(255, 255, 255, 0.16);

  border-radius: 9999px;

  background: rgba(255, 255, 255, 0.1);

  color: white;

  font-size: 13px;
  font-weight: 500;

  backdrop-filter: blur(10px);

  transition: background 0.2s ease, transform 0.2s ease, box-shadow 0.2s ease;
}

.unlock-button:hover {
  background: rgba(255, 255, 255, 0.18);

  transform: translateY(-2px);

  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.25);
}

.unlock-button:active {
  transform: scale(0.96);
}

/*
|--------------------------------------------------------------------------
| SLIDER BUTTON
|--------------------------------------------------------------------------
*/

.slider-button {
  position: absolute;

  top: 50%;

  z-index: 40;

  width: 48px;
  height: 48px;

  display: flex;
  align-items: center;
  justify-content: center;

  transform: translateY(-50%);

  border: 1px solid rgba(255, 255, 255, 0.15);

  border-radius: 9999px;

  background: rgba(0, 0, 0, 0.18);

  color: white;

  backdrop-filter: blur(15px);

  transition: background 0.2s ease, transform 0.2s ease, opacity 0.3s ease;
}

.slider-button:hover {
  background: rgba(255, 255, 255, 0.18);

  transform: translateY(-50%) scale(1.08);
}

.slider-button:active {
  transform: translateY(-50%) scale(0.94);
}

/*
|--------------------------------------------------------------------------
| SLIDER DOTS
|--------------------------------------------------------------------------
*/

.slider-dot {
  width: 7px;
  height: 7px;

  padding: 0;

  border: 0;

  border-radius: 9999px;

  background: rgba(255, 255, 255, 0.4);

  transition: width 0.3s ease, background 0.3s ease;
}

.slider-dot.active {
  width: 24px;

  background: white;
}

/*
|--------------------------------------------------------------------------
| UI HIDE
|--------------------------------------------------------------------------
*/

.ui-hidden {
  opacity: 0;

  pointer-events: none;

  transition: opacity 0.4s ease;
}

/*
|--------------------------------------------------------------------------
| TRANSITIONS
|--------------------------------------------------------------------------
*/

.screensaver-enter-active,
.screensaver-leave-active {
  transition: opacity 0.7s ease, transform 0.7s ease;
}

.screensaver-enter-from,
.screensaver-leave-to {
  opacity: 0;

  transform: scale(1.025);
}

.screensaver-enter-to,
.screensaver-leave-from {
  opacity: 1;

  transform: scale(1);
}

/*
|--------------------------------------------------------------------------
| BACKGROUND TRANSITION
|--------------------------------------------------------------------------
*/

.background-enter-active,
.background-leave-active {
  transition: opacity 1.2s ease, transform 6s ease;
}

.background-enter-from {
  opacity: 0;

  transform: scale(1.06);
}

.background-leave-to {
  opacity: 0;

  transform: scale(1);
}

/*
|--------------------------------------------------------------------------
| ANIMATIONS
|--------------------------------------------------------------------------
*/

@keyframes backgroundZoom {
  from {
    transform: scale(1);
  }

  to {
    transform: scale(1.045);
  }
}

@keyframes clockEnter {
  from {
    opacity: 0;

    transform: translateY(35px) scale(0.96);
  }

  to {
    opacity: 1;

    transform: translateY(0) scale(1);
  }
}

@keyframes dateEnter {
  from {
    opacity: 0;

    transform: translateY(15px);
  }

  to {
    opacity: 1;

    transform: translateY(0);
  }
}

@keyframes cardEnter {
  from {
    opacity: 0;

    transform: translateY(30px) scale(0.96);
  }

  to {
    opacity: 1;

    transform: translateY(0) scale(1);
  }
}

/*
|--------------------------------------------------------------------------
| MOBILE
|--------------------------------------------------------------------------
*/

@media (max-width: 640px) {
  .user-card {
    min-width: 250px;

    padding: 18px 22px 22px;
  }

  .avatar-wrapper,
  .avatar-image,
  .avatar-fallback {
    width: 76px;
    height: 76px;
  }

  .lock-clock {
    font-size: 4rem;
  }

  .lock-date {
    font-size: 0.9rem;
  }

  .slider-button {
    width: 40px;
    height: 40px;
  }
}
</style>
