<?php

use App\Http\Controllers\SiteController;
use App\Mail\QuoteRequestReceived;
use App\Models\LinguistApplication;
use App\Models\NewsletterSubscriber;
use App\Models\ProjectEnquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

// Every public route exists in English (site root, the default) and in French (under /fr).
$siteRoutes = function (): void {
    Route::get('/', [SiteController::class, 'home']);
    Route::get('/services/{slug}', [SiteController::class, 'service'])->where('slug', '[a-z0-9-]+');
    Route::get('/knowledge-center/{slug}', [SiteController::class, 'article'])->where('slug', '[a-z0-9-]+');

    Route::post('/contact', function (Request $request) {
        $form = config('site.quote_form');

        // Hidden honeypot field: real visitors leave it empty, simple bots fill it in.
        if ($request->filled('website')) {
            return redirect('/thank-you');
        }

        $enquiry = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:40'],
            'organization' => ['nullable', 'string', 'max:160'],
            'service' => ['required', Rule::in(array_keys($form['services']))],
            'industry' => ['nullable', Rule::in($form['industries'])],
            'languages' => ['nullable', 'array'],
            'languages.*' => [Rule::in($form['languages'])],
            'other_language' => ['nullable', 'string', 'max:120'],
            'volume' => ['nullable', 'string', 'max:160'],
            'deadline' => ['nullable', 'string', 'max:120'],
            'message' => ['required', 'string', 'max:5000'],
            'attachment' => ['nullable', 'file', 'max:'.$form['max_upload_kb'], 'extensions:'.implode(',', $form['file_types'])],
            'consent' => ['accepted'],
        ], [
            'service.required' => __('Please choose the service you need.'),
            'message.required' => __('Please add a short project summary.'),
            'consent.accepted' => __('Please confirm that we may use these details to respond to your request.'),
            'attachment.max' => __('The file must be 50MB or smaller. For larger files, tell us and we will share a transfer link.'),
        ]);

        // Files are kept on the private disk (never publicly reachable).
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $enquiry['attachment_path'] = $file->store('enquiries');
            $enquiry['attachment_name'] = $file->getClientOriginalName();
        }

        unset($enquiry['attachment'], $enquiry['consent']);
        $enquiry['service'] = $form['services'][$enquiry['service']];
        $enquiry['consented_at'] = now();

        $saved = ProjectEnquiry::create($enquiry);

        // Notify the team by email. The request is already saved, so a mail problem never loses it.
        if ($recipient = config('mail.quote_recipient')) {
            try {
                Mail::to($recipient)->send(new QuoteRequestReceived($saved));
            } catch (Throwable $e) {
                report($e);
            }
        }

        return redirect('/thank-you')->with('enquiry_submitted', true);
    })->name('contact.submit')->middleware('throttle:contact-form');

    Route::post('/careers/apply', function (Request $request) {
        $form = config('site.application_form');

        // Hidden honeypot field: real visitors leave it empty, simple bots fill it in.
        if ($request->filled('website')) {
            return redirect('/thank-you');
        }

        $application = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:40'],
            'location' => ['required', 'string', 'max:160'],
            'native_languages' => ['required', 'array', 'min:1'],
            'native_languages.*' => [Rule::in($form['native_languages'])],
            'language_pairs' => ['required', 'string', 'max:500'],
            'expertise' => ['required', Rule::in($form['expertise'])],
            'tools' => ['nullable', 'array'],
            'tools.*' => [Rule::in($form['tools'])],
            'cv' => ['required', 'file', 'mimes:pdf', 'max:'.$form['max_upload_kb']],
            'consent' => ['accepted'],
        ], [
            'native_languages.required' => __('Please select at least one native language.'),
            'cv.required' => __('Please attach your CV and portfolio as a PDF.'),
            'cv.mimes' => __('Your CV and portfolio must be a PDF file.'),
            'consent.accepted' => __('Please confirm that we may use your details to assess your application.'),
        ]);

        // CVs are kept on the private disk (never publicly reachable).
        $cv = $request->file('cv');
        $application['cv_path'] = $cv->store('applications');
        $application['cv_name'] = $cv->getClientOriginalName();
        $application['consented_at'] = now();
        unset($application['cv'], $application['consent']);

        LinguistApplication::create($application);

        return redirect('/thank-you')->with('application_submitted', true);
    })->name('careers.apply')->middleware('throttle:careers-form');

    Route::post('/newsletter', function (Request $request) {
        // Hidden honeypot field: real visitors leave it empty, simple bots fill it in.
        if ($request->filled('website')) {
            return redirect()->to(url()->previous().'#newsletter');
        }

        $validator = Validator::make($request->only('email'), [
            'email' => ['required', 'email', 'max:255'],
        ]);

        // Return to the footer form (not the top of the page) so the message is visible.
        if ($validator->fails()) {
            return redirect()->to(url()->previous().'#newsletter')->withErrors($validator, 'newsletter')->withInput();
        }

        $subscriber = NewsletterSubscriber::firstOrCreate(['email' => strtolower(trim($validator->validated()['email']))]);

        return redirect()->to(url()->previous().'#newsletter')
            ->with('newsletter_status', $subscriber->wasRecentlyCreated ? 'subscribed' : 'already');
    })->name('newsletter.subscribe')->middleware('throttle:newsletter-form');

    Route::get('/thank-you', [SiteController::class, 'thankYou']);

    // Core pages (the list of pages is fixed; their content is managed in the admin dashboard).
    Route::get('/{slug}', [SiteController::class, 'page'])->whereIn('slug', array_keys(config('site.pages')));
};

// Admin dashboard (registered first so /admin is never treated as a public page).
require __DIR__.'/admin.php';

Route::middleware('locale:en')->group($siteRoutes);
Route::prefix('fr')->name('fr.')->middleware('locale:fr')->group($siteRoutes);
