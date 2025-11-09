<?php

namespace App\Http\Controllers\Print;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\View;
use Spatie\LaravelPdf\Enums\Format;
use Spatie\LaravelPdf\Facades\Pdf;

abstract class BasePrintController
{
    abstract protected function view(): string;

    protected function headerView(): string
    {
        $orgId = auth()->user()?->organisation_id;
        $companyHeader = "pdf.company.{$orgId}.header";

        return View::exists($companyHeader)
            ? $companyHeader
            : 'pdf.layouts.header';
    }

    protected function footerView(): string
    {
        $orgId = auth()->user()?->organisation_id;
        $companyFooter = "pdf.company.{$orgId}.footer";

        return View::exists($companyFooter)
            ? $companyFooter
            : 'pdf.layouts.footer';
    }

    protected function fileName(Model $record): string
    {
        return class_basename($record) . '-' . $record->id . '.pdf';
    }

    public function inline($record)
    {
        return $this->buildPdf($record)->inline($this->fileName($record));
    }

    public function download($record)
    {
        return $this->buildPdf($record)->download($this->fileName($record));
    }

    protected function buildPdf(Model $record)
    {
        return Pdf::view($this->view(), ['record' => $record])
            ->headerView($this->headerView())
            ->footerView($this->footerView())
            ->format(Format::A4)
            ->name($this->fileName($record));
    }
}
