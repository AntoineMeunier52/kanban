<template>
  <div class="tabs-bubble">
    <!-- Tabs select -->
    <div class="tabs-nav">
      <div
        v-for="(tab, index) in tabs"
        :key="index"
        @click="handleClick(index)"
        :class="getTabClass(index)"
        class="tab-bubble"
      >
        <span class="tab-bubble-text" :class="getTabClass(index)">
          {{ tab }}
        </span>
      </div>
    </div>

    <!-- Components -->
    <div>
      <transition name="slide" :mode="transitionMode">
        <div :key="activeTab">
          <slot :name="`tab-${activeTab}`"></slot>
        </div>
      </transition>
    </div>
  </div>
</template>

<script setup lang="ts">
const props = defineProps({
  tabs: {
    type: Array,
    required: true,
  },
});

const activeTab = ref(0);
const animationDirection = ref("out");

const getTabClass = computed(() => {
  return (index: number) =>
    activeTab.value === index ? "active" : "no-active";
});

const transitionMode = computed(() => {
  return animationDirection.value === "out" ? "out-in" : "in-out";
});

const handleClick = (index: number) => {
  if (activeTab.value < index) {
    animationDirection.value = "in";
  } else {
    animationDirection.value = "out";
  }
  activeTab.value = index;
};
</script>

<style scoped>
@reference "../assets/css/main.css";

.tabs-bubble {
  @apply w-full;
}

.tabs-nav {
  @apply flex gap-[8px] p-[2px] my-[8px] bg-solid2-creme rounded mb-[44px] justify-around;
  @apply transition-all duration-300 ease-in-out overflow-hidden;
}

.tab-bubble {
  @apply w-full text-center px-[16px] py-[8px] border-0 rounded bg-transparent text-text-secondary font-bold cursor-pointer;
}

.tab-bubble.active {
  @apply bg-primary-creme text-text-primary;
}

.tab-bubble-text.no-active {
  @apply relative;
}

.tab-bubble-text.no-active::before {
  @apply absolute bottom-[-3px] left-[50%] translate-x-[-50%] h-[2px] w-0 transition-all duration-300 ease-in-out bg-text-secondary;
  content: "";
}

.tab-bubble.no-active:hover .tab-bubble-text.no-active::before {
  @apply w-full;
}

/* Animations plus visibles */
.slide-enter-active {
  @apply transition-all duration-500 ease-out;
}

.slide-leave-active {
  @apply transition-all duration-300 ease-in;
}

.slide-enter-from {
  @apply translate-x-8 opacity-0 scale-95;
}

.slide-leave-to {
  @apply -translate-x-8 opacity-0 scale-95;
}
</style>
