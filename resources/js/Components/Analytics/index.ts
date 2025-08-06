export { default as AnalyticsMetric } from './AnalyticsMetric.vue'

export type Trend = 'increasing' | 'none' | 'decreasing'
export type TrendStyle = 'positive' | 'neutral' | 'negative'

export interface AnalyticsMetricValue {
  title: string
  description: string | null
  value: string
  numericValue: number
  previousValue: string | null
  change: {
    value: string
    difference: string
    trend: Trend
    trendStyle: TrendStyle
    isInversed: boolean
    numericValue: number
    showTooltip: boolean
    percentage: number
  } | null
  link: {
    label: string
    url: string
    isExternal: boolean
  } | null
}

export const resolveTrendColor = (style: TrendStyle) => {
  if (style == 'negative') {
    return 'text-red-600'
  } else if (style == 'positive') {
    return 'text-green-600';
  }

  return 'text-muted-foreground'
}
