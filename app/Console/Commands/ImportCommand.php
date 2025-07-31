<?php

namespace App\Console\Commands;

use App\Enums\Country;
use App\Enums\PaymentMethod;
use App\Enums\UserAccountRole;
use App\Models\Account;
use App\Models\Address;
use App\Models\Company;
use App\Models\DocumentTemplate;
use App\Models\Invoice;
use App\Models\NumberSequence;
use App\Models\Upload;
use App\Models\User;
use App\Support\NumberSequenceFormatter;
use Brick\Money\Money;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Fluent;
use Illuminate\Support\Str;

class ImportCommand extends Command
{
    protected $signature = 'import {path}';

    protected $description = 'Import data from legacy app';

    public function handle(): int
    {
        $path = $this->argument('path');

        if (! File::exists($path)) {
            $this->error("The file [$path] does not exist");
            return self::FAILURE;
        }

        collect(File::json($path))->sortBy(fn ($item) => Arr::get([
            'stovka.peter@gmail.com' => 1,
            'faktury@stacktrace.sk' => 2,
            'peter@stovka.eu' => 3,
        ], $item['email'], 4))->map(function (array $data) {
            $data = new Fluent($data);

            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'password' => Hash::make('password'),
                'created_at' => Carbon::createFromTimestamp($data['created_at']),
            ]);

            $address = new Address([
                'line_one' => $data->get('company.address.line_one'),
                'line_two' => $data->get('company.address.line_two'),
                'line_three' => $data->get('company.address.line_three'),
                'city' => $data->get('company.address.city'),
                'postal_code' => $data->get('company.address.postal_code'),
                'country' => $this->resolveCountry($data->get('company.address.country')),
            ]);
            $address->save();

            $company = new Company([
                'business_name' => $data->get('company.name'),
                'business_id' => $data->get('company.business_id'),
                'vat_id' => $data->get('company.vat_id'),
                'eu_vat_id' => $data->get('company.eu_vat_id'),
                'email' => $data->get('company.email'),
                'phone_number' => $data->get('company.phone'),
                'website' => $data->get('company.website'),
                'additional_info' => $data->get('company.additional_invoice_info'),
                'bank_name' => $data->get('settings.bank_name'),
                'bank_bic' => $data->get('settings.bank_bic'),
                'bank_account_number' => $data->get('settings.bank_account_number'),
                'bank_account_iban' => $data->get('settings.bank_account_iban'),
            ]);
            $company->address()->associate($address);
            $company->save();

            $account = new Account([
                'vat_enabled' => $data->boolean('company.is_tax_payer'),
                'default_vat_rate' => 23,
                'invoice_due_days' => $data->get('settings.default_payment_due'),
                'invoice_numbering_format' => $data->get('settings.invoice_numbering_format'),
                'invoice_variable_symbol_format' => $data->get('settings.invoice_numbering_format'),
                'invoice_payment_method' => $this->resolvePaymentMethod($data->get('settings.default_payment_method')),
                'invoice_footer_note' => $data->get('settings.footer_note'),
            ]);
            $account->invoiceTemplate()->associate($this->resolveTemplate($data->get('settings.template')));
            $account->company()->associate($company);
            $account->save();

            if ($signatureImage = $data->get('settings.signature')) {
                $signature = $this->storeFile($account->id, $signatureImage);
                $account->invoiceSignature()->associate($signature);
                $account->save();
            }

            $account->users()->attach($user, ['role' => UserAccountRole::Owner]);

            $data->collect('invoices')->each(function (array $invoice) use ($account) {
                $invoice = new Fluent($invoice);

                $supplierAddress = new Address([
                    'line_one' => $invoice->get('supplier.address.line_one'),
                    'line_two' => $invoice->get('supplier.address.line_two'),
                    'line_three' => $invoice->get('supplier.address.line_three'),
                    'city' => $invoice->get('supplier.address.city'),
                    'postal_code' => $invoice->get('supplier.address.postal_code'),
                    'country' => $this->resolveCountry($invoice->get('supplier.address.country')),
                ]);
                $supplierAddress->save();

                $supplier = new Company([
                    'business_name' => $invoice->get('supplier.name'),
                    'business_id' => $invoice->get('supplier.business_id'),
                    'vat_id' => $invoice->get('supplier.vat_id'),
                    'eu_vat_id' => $invoice->get('supplier.eu_vat_id'),
                    'email' => $invoice->get('supplier.email'),
                    'phone_number' => $invoice->get('supplier.phone'),
                    'website' => $invoice->get('supplier.website'),
                    'additional_info' => $invoice->get('supplier.additional_invoice_info'),
                    'bank_name' => $invoice->get('bank_name'),
                    'bank_bic' => $invoice->get('bank_bic'),
                    'bank_account_number' => $invoice->get('bank_account_number'),
                    'bank_account_iban' => $invoice->get('bank_account_iban'),
                ]);
                $supplier->address()->associate($supplierAddress);
                $supplier->save();

                $customerAddress = new Address([
                    'line_one' => $invoice->get('customer.address.line_one'),
                    'line_two' => $invoice->get('customer.address.line_two'),
                    'line_three' => $invoice->get('customer.address.line_three'),
                    'city' => $invoice->get('customer.address.city'),
                    'postal_code' => $invoice->get('customer.address.postal_code'),
                    'country' => $this->resolveCountry($invoice->get('customer.address.country')),
                ]);
                $customerAddress->save();

                $customer = new Company([
                    'business_name' => $invoice->get('customer.name'),
                    'business_id' => $invoice->get('customer.business_id'),
                    'vat_id' => $invoice->get('customer.vat_id'),
                    'eu_vat_id' => $invoice->get('customer.eu_vat_id'),
                    'email' => $invoice->get('customer.email'),
                    'phone_number' => $invoice->get('customer.phone'),
                    'website' => $invoice->get('customer.website'),
                    'additional_info' => $invoice->get('customer.additional_invoice_info'),
                ]);
                $customer->address()->associate($customerAddress);
                $customer->save();

                $signature = null;
                if ($signatureSource = $invoice->get('signature')) {
                    $signature = $this->storeFile($account->id, $signatureSource, true);
                }

                $importedInvoice = new Invoice([
                    'draft' => false,
                    'sent' => $invoice->get('status') == 'sent',
                    'paid' => $invoice->boolean('paid'),
                    'locked' => true,
                    'public_invoice_number' => $invoice->get('invoice_number'),
                    'payment_method' => $this->resolvePaymentMethod($invoice->get('payment_method')),
                    'variable_symbol' => $invoice->get('variable_symbol'),
                    'currency' => 'EUR',
                    'issued_by' => $invoice->get('issued_by'),
                    'issued_by_phone_number' => $invoice->get('issued_by_phone'),
                    'issued_by_email' => $invoice->get('issued_by_email'),
                    'issued_by_website' => $invoice->get('issued_by_website'),
                    'vat_enabled' => $account->vat_enabled ? $invoice->boolean('price_with_vat') : false,
                    'footer_note' => $invoice->get('footer_note'),
                    'vat_reverse_charge' => $invoice->boolean('vat_reverse_charge'),
                    'show_pay_by_square' => $invoice->boolean('show_pay_by_square'),
                    'issued_at' => Carbon::createFromTimestamp($invoice->get('issued_at')),
                    'supplied_at' => Carbon::createFromTimestamp($invoice->get('supplied_at')),
                    'payment_due_to' => Carbon::createFromTimestamp($invoice->get('payment_due_to')),
                    'created_at' => Carbon::createFromTimestamp($invoice->get('created_at')),
                ]);
                $importedInvoice->template()->associate($this->resolveTemplate($invoice->get('template')));
                $importedInvoice->signature()->associate($signature);
                $importedInvoice->supplier()->associate($supplier);
                $importedInvoice->customer()->associate($customer);
                $importedInvoice->account()->associate($account);
                $importedInvoice->save();

                $invoice
                    ->collect('lines')
                    ->sortBy('position')
                    ->values()
                    ->each(function (array $line, int $idx) use ($importedInvoice) {
                        $line = new Fluent($line);

                        $toMoney = function ($value) {
                            if (is_numeric($value)) {
                                return Money::ofMinor($value, 'EUR');
                            }

                            return null;
                        };

                        $vatRate = $line->get('tax_rate');

                        $unitPriceVatExclusive = $toMoney($line->get('unit_price'));
                        $totalPriceVatExclusive = $toMoney($line->get('total_price'));
                        $totalPriceVatInclusive = $toMoney($line->get('total_price_vat_inclusive'));

                        if (! $importedInvoice->vat_enabled) {
                            $totalPriceVatInclusive = null;
                            $vatRate = null;
                        }

                        $importedInvoice->lines()->create([
                            'position' => $idx + 1,
                            'title' => $line->get('title'),
                            'description' => $line->get('description'),
                            'unit' => $line->get('uom'),
                            'quantity' => $line->get('quantity'),
                            'vat_rate' => $vatRate,
                            'unit_price_vat_exclusive' => $unitPriceVatExclusive,
                            'total_price_vat_exclusive' => $totalPriceVatExclusive,
                            'total_price_vat_inclusive' => $totalPriceVatInclusive,
                            'currency' => 'EUR',
                            'created_at' => Carbon::createFromTimestamp($line->get('created_at')),
                        ]);
                    });

                $importedInvoice->load('lines');
                $importedInvoice->calculateTotals();
            });

            $account->load('invoices');
            $account->invoices->sortBy('public_invoice_number')->values()->each(function (Invoice $invoice) {
                $number = $invoice->public_invoice_number;

                $date = $invoice->supplied_at;

                if (Str::length($number) === 10) {
                    $format = "RRRRMMCCCC";

                    $date = now()
                        ->startOfMonth()
                        ->setYear((int) Str::substr($number, 0, 4))
                        ->setMonth((int) ltrim(Str::substr($number, 4, 2), '0'));
                } else if (Str::length($number) === 8) {
                    $format = "RRRRCCCC";
                } else if (Str::length($number) === 6) {
                    $format = 'RRCCCC';
                } else {
                    throw new \RuntimeException("Unable to guess format: {$number}");
                }

                $formatter = new NumberSequenceFormatter($format, $date);
                $token = $formatter->formatSequenceToken();

                /** @var NumberSequence $sequence */
                $sequence = $invoice->account->numberSequences()->where('sequence_token', $token)->first();
                if (! $sequence) {
                    $sequence = $invoice->account->numberSequences()->create([
                        'sequence_token' => $token,
                        'next_number' => 1,
                        'format' => $format,
                    ]);
                }

                $nextNumber = $sequence->next_number;
                $invoice->numberSequence()->associate($sequence);
                $invoice->invoice_number = $nextNumber;
                $invoice->save();

                $sequence->incrementNextNumber();
            });
        });

        return self::SUCCESS;
    }

    protected function storeFile(int $userId, string $contents, bool $copy = false): Upload
    {
        $name = sha1($contents.$userId);

        [$data, $base] = explode(';', $contents);
        $extension = match (Arr::last(explode(':', $data))) {
            'image/png' => 'png',
            'image/jpg' => 'jpg',
            'image/jpeg' => 'jpeg',
            default => throw new \InvalidArgumentException("Unable to guess extension")
        };

        $fileName = $name.'.'.$extension;

        $path = 'uploads/'.$fileName;

        if ($upload = Upload::query()->where('disk', 'public')->where('file_path', $path)->first()) {
            if ($copy) {
                $copied = $upload->replicate();
                $copied->save();
                return $copied;
            }

            return $upload;
        }

        Storage::disk('public')->put($path, base64_decode(Str::replaceFirst('base64,', '', $base)));

        return Upload::create([
            'disk' => 'public',
            'file_path' => $path,
        ]);
    }

    protected function resolvePaymentMethod(int $type): PaymentMethod
    {
        return match ($type) {
            1 => PaymentMethod::Cash,
            2 => PaymentMethod::BankTransfer,
        };
    }

    protected function resolveTemplate(string $template): DocumentTemplate
    {
        return match ($template) {
            'minima' => DocumentTemplate::query()->where('package', '@stacktrace/minimal')->firstOrFail(),
            'stacktrace' => DocumentTemplate::query()->where('package', '@stacktrace/stacktrace')->firstOrFail(),
        };
    }

    protected function resolveCountry(?string $country): ?Country
    {
        if (is_null($country)) {
            return null;
        }

        return match ($country) {
            "Slovensko", "Slovenská republika" => Country::Slovakia,
            "Nemecko", "Germany" => Country::Germany,
            "United Arab Emirates" => Country::UnitedArabEmirates,
            default => throw new \InvalidArgumentException("The country could not be found: [$country]"),
        };
    }
}
