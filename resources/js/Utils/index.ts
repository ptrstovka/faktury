export * from './keyboard.ts'

import { clsx, type ClassValue } from 'clsx';
import { twMerge } from 'tailwind-merge';
import currency from "currency.js";
import { nextTick } from "vue";
import { toast } from "vue-sonner";

export function cn(...inputs: ClassValue[]) {
  return twMerge(clsx(inputs));
}

export function isValidNumber(value: string | null | undefined): boolean {
  // TODO: overiť regexom aby tu boli len čisla, medzery čiarka alebo bodka
  const numericValue = Number(value)

  return !!(value && !isNaN(numericValue));
}

export function decimalPlaces(value: string, separator: string = '.'): number {
  if (! value.includes(separator)) {
    return 0
  }

  return value.split(separator)[1].length
}

/**
 * Create Money value for internal calculations.
 */
export function createMoney(value: string | number, precision: number, symbol: string = '€') {
  return currency(value, {
    precision: precision,
    symbol: symbol,
    decimal: '.',
    separator: '',
  })
}

/**
 * Create Money value for internal calculations.
 */
export function createMoneyFromMinor(value: string | number, precision: number, symbol: string = '€') {
  return currency(value, {
    fromCents: true,
    precision: precision,
    symbol: symbol,
    decimal: '.',
    separator: '',
  })
}

export const notifyAboutFirstVisibleError = (message: string) => {
  toast.error(message, {
    style: {
      background: "var(--destructive)",
      color: "var(--destructive-foreground)",
    },
  })

  nextTick(() => {
    const firstElementWithError = document.querySelector(".has-error")
    if (firstElementWithError) {
      const rect = firstElementWithError.getBoundingClientRect()
      const isVisible =
        rect.top >= 0 &&
        rect.left >= 0 &&
        rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
        rect.right <= (window.innerWidth || document.documentElement.clientWidth)

      if (!isVisible) {
        window.scrollTo({
          top: rect.top + window.pageYOffset - 80,
          behavior: "smooth",
        })
      }
    }
  })
}
