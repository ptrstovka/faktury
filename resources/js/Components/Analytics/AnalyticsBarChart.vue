<template>
  <BarChart
    :data="data"
    :categories="categories"
    index="name"
    :y-formatter="formatY"
    :show-legend="showLegend"
    :value-formatter="formatValue"
  />
</template>

<script setup lang="ts">
import { computed } from "vue";
import type { AnalyticsChartValue } from ".";
import { formatMinorMoney } from '@/Utils'
import { BarChart } from '@/Components/ChartBar'

const props = defineProps<AnalyticsChartValue>()

const categories = computed<Array<string>>(() => {
  const points = props.points

  if (points.length > 0) {
    return points[0].values.map(it => it.label)
  }

  return []
})

const data = computed(() => {
  return props.points.map(point => {
    const value: Record<string, any> = { name: point.group }

    point.values.forEach(pointValue => {
      value[pointValue.label] = pointValue.value
    })

    return value
  })
})

const formatY = (tick: number | Date, _: number, __: number[] | Date[]) => {
  if (props.format === 'money' && typeof tick === 'number') {
    return formatMinorMoney(tick)
  }

  return `${tick}`
}

const formatValue = (tick: number) => {
  if (props.format === 'money') {
    return formatMinorMoney(tick)
  }

  if ((props.format === 'number' || props.format === 'percentage') && props.maxPrecision >= 0) {
    const value = parseFloat(`${parseFloat(`${tick}`).toFixed(props.maxPrecision)}`)

    return props.format === 'percentage' ? `${value}%` : `${value}`
  }

  return `${tick}`
}
</script>
