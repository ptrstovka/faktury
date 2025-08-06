<template>
  <Head title="Prehľad"/>

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="px-4 flex flex-col gap-6">
      <div class="flex flex-row items-end justify-between pt-6">
        <Heading :title="`Prehľad za rok ${year}`" class="mb-0" />

        <div class="inline-flex flex-row gap-2">
          <DropdownMenu>
            <DropdownMenuTrigger as-child>
              <Button variant="outline" size="sm"><CalendarIcon class="size-4" /> Obdobie</Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="end">
              <DropdownMenuCheckboxItem @select="filter.period = y" :model-value="filter.period == y" v-for="y in allYears">{{ y }}</DropdownMenuCheckboxItem>
            </DropdownMenuContent>
          </DropdownMenu>
        </div>
      </div>

      <div class="grid auto-rows-min gap-4 md:grid-cols-3">
        <AnalyticsMetric v-for="metric in topMetrics" v-bind="metric" />
      </div>
    </div>

  </AppLayout>
</template>

<script setup lang="ts">
import { type AnalyticsMetricValue, AnalyticsMetric } from "@/Components/Analytics";
import Heading from "@/Components/Heading.vue";
import AppLayout from '@/Layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/Types';
import { Head } from '@inertiajs/vue3';
import { useFilter } from "@stacktrace/ui";
import { DropdownMenu, DropdownMenuCheckboxItem, DropdownMenuTrigger, DropdownMenuContent } from '@/Components/DropdownMenu'
import { Button } from '@/Components/Button'
import { CalendarIcon } from 'lucide-vue-next'

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Dashboard',
    href: '/dashboard',
  },
];

const props = defineProps<{
  year: string
  topMetrics: Array<AnalyticsMetricValue>
  defaultYear: number
  allYears: Array<number>
}>()

const filter = useFilter({
  period: props.defaultYear,
})
</script>
