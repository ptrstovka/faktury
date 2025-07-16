import { clsx, type ClassValue } from 'clsx';
import { twMerge } from 'tailwind-merge';
import currency from "currency.js";

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
