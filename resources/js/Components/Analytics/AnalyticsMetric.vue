<template>
  <Card class="flex flex-col">
    <CardHeader class="flex flex-row items-center justify-between space-y-0">
      <CardTitle class="text-sm font-medium">{{ title }}</CardTitle>
      <TooltipProvider v-if="description">
        <Tooltip :delay-duration="0">
          <TooltipTrigger>
            <InfoIcon class="h-4 w-4 text-muted-foreground"/>
          </TooltipTrigger>
          <TooltipContent class="max-w-xs">
            {{ description }}
          </TooltipContent>
        </Tooltip>
      </TooltipProvider>
    </CardHeader>
    <CardContent class="flex flex-col flex-1 items-start">
      <div class="text-2xl font-bold">
        {{ value }}
      </div>

      <TooltipProvider v-if="change">
        <Tooltip :delay-duration="0" :disabled="! change.showTooltip">
          <TooltipTrigger :class="cn('inline-flex items-center gap-1 mt-2 text-sm font-medium', resolveTrendColor(change.trendStyle))">
            <TrendingUpIcon v-if="change.trend === 'increasing'" class="w-4 h-4"/>
            <TrendingDownIcon v-else-if="change.trend === 'decreasing'" class="w-4 h-4"/>
            {{ change.value }}
          </TooltipTrigger>
          <TooltipContent side="right">{{ change.difference }}</TooltipContent>
        </Tooltip>
      </TooltipProvider>

      <div class="flex-1"></div>

      <component
        v-if="link"
        class="text-xs inline-flex items-center gap-2 mt-4 font-medium text-muted-foreground hover:text-foreground transition-colors"
        :is="link.isExternal ? 'a' : Link"
        :href="link.url"
      >{{ link.label }} <MoveRightIcon class="w-4 h-4"/></component>
    </CardContent>
  </Card>
</template>

<script setup lang="ts">
import { cn } from '@/Utils'
import { TrendingUpIcon, TrendingDownIcon, InfoIcon, MoveRightIcon } from "lucide-vue-next";
import { Card, CardHeader, CardContent, CardTitle } from '@/Components/Card'
import { Link } from '@inertiajs/vue3'
import { Tooltip, TooltipProvider, TooltipContent, TooltipTrigger } from '@/Components/Tooltip'
import { type AnalyticsMetricValue, resolveTrendColor } from '.'

defineProps<AnalyticsMetricValue>()
</script>
