import { createInvoiceLine, type InvoiceLine } from "@/Components/InvoiceLineInput";
import type { InvoiceDetailProps } from "@/Pages/Invoices";
import { useForm } from "@inertiajs/vue3";
import { computed, type ComputedRef, inject, provide } from "vue";

interface VatBreakdownItem {
  vatRate: number
  base: number
  total: number
}

export function useInvoiceForm(props: ComputedRef<InvoiceDetailProps>) {
  const form = useForm(() => ({
    issued_at: props.value.issuedAt || "",
    supplied_at: props.value.suppliedAt || "",
    payment_due_to: props.value.paymentDueTo || "",
    public_invoice_number: props.value.publicInvoiceNumber || "",

    supplier_business_name: props.value.supplier.businessName || "",
    supplier_business_id: props.value.supplier.businessId || "",
    supplier_vat_id: props.value.supplier.vatId || "",
    supplier_eu_vat_id: props.value.supplier.euVatId || "",
    supplier_email: props.value.supplier.email || "",
    supplier_phone_number: props.value.supplier.phoneNumber || "",
    supplier_website: props.value.supplier.website || "",
    supplier_additional_info: props.value.supplier.additionalInfo || "",
    supplier_address_line_one: props.value.supplier.addressLineOne || "",
    supplier_address_line_two: props.value.supplier.addressLineTwo || "",
    supplier_address_line_three: props.value.supplier.addressLineThree || "",
    supplier_address_city: props.value.supplier.addressCity || "",
    supplier_address_postal_code: props.value.supplier.addressPostalCode || "",
    supplier_address_country: props.value.supplier.addressCountry || "",

    customer_business_name: props.value.customer.businessName || "",
    customer_business_id: props.value.customer.businessId || "",
    customer_vat_id: props.value.customer.vatId || "",
    customer_eu_vat_id: props.value.customer.euVatId || "",
    customer_email: props.value.customer.email || "",
    customer_phone_number: props.value.customer.phoneNumber || "",
    customer_website: props.value.customer.website || "",
    customer_additional_info: props.value.customer.additionalInfo || "",
    customer_address_line_one: props.value.customer.addressLineOne || "",
    customer_address_line_two: props.value.customer.addressLineTwo || "",
    customer_address_line_three: props.value.customer.addressLineThree || "",
    customer_address_city: props.value.customer.addressCity || "",
    customer_address_postal_code: props.value.customer.addressPostalCode || "",
    customer_address_country: props.value.customer.addressCountry || "",

    template: props.value.template,
    footer_note: props.value.footerNote || "",
    issued_by: props.value.issuedBy || "",
    issued_by_email: props.value.issuedByEmail || "",
    issued_by_phone_number: props.value.issuedByPhoneNumber || "",
    issued_by_website: props.value.issuedByWebsite || "",

    payment_method: props.value.paymentMethod,
    bank_name: props.value.bankName || "",
    bank_address: props.value.bankAddress || "",
    bank_bic: props.value.bankBic || "",
    bank_account_number: props.value.bankAccountNumber || "",
    bank_account_iban: props.value.bankAccountIban || "",
    variable_symbol: props.value.variableSymbol || "",
    specific_symbol: props.value.specificSymbol || "",
    constant_symbol: props.value.constantSymbol || "",
    show_pay_by_square: props.value.showPayBySquare,

    vat_reverse_charge: props.value.vatReverseCharge,
    vat_enabled: props.value.vatEnabled,

    lines: props.value.lines.map((line) => ({ ...line })) as Array<any>, // Array<InvoiceLine>
  }))

  const lineErrors = computed<Array<Partial<Record<keyof InvoiceLine, string>>>>(
    () => {
      const formErrors = form.errors as Record<string, string>

      return form.lines.map((_, idx) => {
        const errors: Partial<Record<keyof InvoiceLine, string>> = {}

        Object.keys(formErrors).forEach((key) => {
          if (key.startsWith(`lines.${idx}.`)) {
            const lineError = formErrors[key]
            const attribute = key
              .replace(`lines.${idx}.`, "")
              .split(".")[0] as keyof InvoiceLine

            errors[attribute] = lineError
          }
        })

        return errors
      })
    },
  )

  const clearLineError = (key: keyof InvoiceLine, index: number) => {
    const field = `lines.${index}.${key}`
    form.clearErrors(field as any)
  }

  const clearLineErrors = () => {
    Object.keys(form.errors).forEach((key) => {
      if (key.startsWith(`lines.`)) {
        form.clearErrors(key as any)
      }
    })
    form.clearErrors('lines')
  }

  const addLine = () => {
    const newLines = form.lines.map((it) => ({ ...it }))
    const emptyLine = createInvoiceLine()
    emptyLine.vat = props.value.defaultVatRate
    newLines.push(emptyLine)
    form.lines = newLines
    clearLineErrors()
  }

  const locked = computed(() => props.value.locked)
  const draft = computed(() => props.value.draft)
  const countries = computed(() => props.value.countries)
  const thousandsSeparator = computed(() => props.value.thousandsSeparator)
  const decimalSeparator = computed(() => props.value.decimalSeparator)
  const quantityPrecision = computed(() => props.value.quantityPrecision)
  const pricePrecision = computed(() => props.value.pricePrecision)
  const templates = computed(() => props.value.templates)
  const paymentMethods = computed(() => props.value.paymentMethods)
  const currency = computed(() => props.value.currency)

  const invoice = computed(() => props.value)

  const totalVatInclusive = computed<number>(() => form.lines.reduce((acc: number, line: InvoiceLine) => {
    return line.totalVatInclusive !== null ? acc + Number(line.totalVatInclusive) : acc
  }, 0))
  const totalVatExclusive = computed<number>(() => form.lines.reduce((acc: number, line: InvoiceLine) => {
    return line.totalVatExclusive !== null ? acc + Number(line.totalVatExclusive) : acc
  }, 0))
  const totalVat = computed<number>(() => Math.max(0, totalVatInclusive.value - totalVatExclusive.value))

  const vatBreakdown = computed<Array<VatBreakdownItem>>(() => {
    const byRate = form.lines.reduce((acc: Record<string, Array<InvoiceLine>>, line: InvoiceLine) => {
      if (line.vat !== null) {
        const rate = `${line.vat}`

        if (!(rate in acc)) {
          acc[rate] = []
        }

        acc[rate].push(line)
      }

      return acc
    }, {})

    return Object.keys(byRate).map<VatBreakdownItem>(key => {
      const lines: Array<InvoiceLine> = byRate[key]

      const totalVatExclusive = lines.reduce((total: number, line: InvoiceLine) => {
        return line.totalVatExclusive !== null ? total + Number(line.totalVatExclusive) : total
      }, 0)

      const totalVatInclusive = lines.reduce((total: number, line: InvoiceLine) => {
        return line.totalVatInclusive !== null ? total + Number(line.totalVatInclusive) : total
      }, 0)

      return {
        vatRate: lines[0].vat!,
        total: Math.max(0, totalVatInclusive - totalVatExclusive),
        base: totalVatExclusive,
      }
    }).sort((a, b) => a.vatRate - b.vatRate)
  })

  return {
    form,
    lineErrors,
    clearLineError,
    clearLineErrors,
    addLine,
    locked,
    draft,
    countries,
    thousandsSeparator,
    decimalSeparator,
    quantityPrecision,
    pricePrecision,
    invoice,
    templates,
    paymentMethods,
    totalVatExclusive,
    totalVatInclusive,
    totalVat,
    vatBreakdown,
    currency,
  }
}

export type InvoiceFormContext = ReturnType<typeof useInvoiceForm>

const InvoiceContextKey = Symbol()

export function provideInvoiceFormContext(context: InvoiceFormContext) {
  provide(InvoiceContextKey, context)
}

export function injectInvoiceFormContext(): InvoiceFormContext {
  return inject<InvoiceFormContext>(InvoiceContextKey)!
}
