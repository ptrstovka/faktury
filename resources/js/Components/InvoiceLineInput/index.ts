export { default as InvoiceLineInput } from './InvoiceLineInput.vue'
export { default as InvoiceLineArrayInput } from './InvoiceLineArrayInput.vue'

export interface InvoiceLine {
  title: string
  description: string
  quantity: number | null
  unit: string
  unitPrice: number | null
  vat: number | null
  totalVatExclusive: number | null
  totalVatInclusive: number | null
}

export function createInvoiceLine(): InvoiceLine {
  return {
    title: '',
    description: '',
    quantity: null,
    unit: '',
    unitPrice: null,
    vat: null,
    totalVatExclusive: null,
    totalVatInclusive: null,
  }
}

export function isEqual(a: InvoiceLine, b: InvoiceLine): boolean {
  return a.title === b.title &&
    a.description === b.description &&
    a.quantity === b.quantity &&
    a.unit === b.unit &&
    a.unitPrice === b.unitPrice &&
    a.vat === b.vat &&
    a.totalVatExclusive === b.totalVatExclusive &&
    a.totalVatInclusive === b.totalVatInclusive
}
