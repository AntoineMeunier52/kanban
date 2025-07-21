<template>
  <div v-if="props.message" class="info-box-success">
    <Icon name="i-lucide-check-circle" class="info-box-icon" />
    <p>{{ props.message }}</p>
    <div v-if="props.showTimer" class="timer-bar-container">
      <div class="timer-bar" :style="{ width: `${timerProgress}%` }"></div>
    </div>
  </div>
</template>

<script setup lang="ts">
const props = defineProps<{
  message: string | null;
  showTimer?: boolean;
  duration?: number; // en secondes, défaut 30s
}>();

const timerProgress = ref(100);
let intervalId: NodeJS.Timeout | null = null;

const duration = props.duration || 30;

// Watcher pour réinitialiser le timer quand showTimer change
watch(
  () => props.showTimer,
  (newValue) => {
    if (newValue && props.message) {
      startTimer();
    } else {
      stopTimer();
    }
  }
);

function startTimer() {
  // Arrêter le timer précédent s'il existe
  stopTimer();

  // Réinitialiser la progression
  timerProgress.value = 100;

  const startTime = Date.now();
  const totalDuration = duration * 1000; // convertir en ms

  intervalId = setInterval(() => {
    const elapsed = Date.now() - startTime;
    const progress = Math.max(0, 100 - (elapsed / totalDuration) * 100);

    timerProgress.value = progress;

    if (progress <= 0) {
      stopTimer();
    }
  }, 100);
}

function stopTimer() {
  if (intervalId) {
    clearInterval(intervalId);
    intervalId = null;
  }
}

onMounted(() => {
  if (props.showTimer && props.message) {
    startTimer();
  }
});

onUnmounted(() => {
  stopTimer();
});
</script>

<style scoped>
@reference "../assets/css/main.css";

.info-box-success {
  @apply relative flex items-center w-full p-[8px] bg-green-300 border border-green-400 rounded mb-[16px];
}

.info-box-success .info-box-icon {
  @apply px-[16px] text-text-primary;
}

.info-box-success .timer-bar-container {
  @apply absolute bottom-0 left-0 right-0 h-[2px] bg-green-500 rounded-b;
}

.info-box-success .timer-bar {
  @apply h-full bg-green-700 transition-all duration-100 ease-linear rounded-b;
}
</style>
