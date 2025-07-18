import currency from "currency.js";

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
