<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Models\SupportReport;

class SupportController extends Controller
{
    public function index()
    {
        return view('support.index');
    }

    public function submit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:dmca,abuse,general,security,appeal',
            'subject' => 'required|string|max:255',
            'description' => 'required|string|max:5000',
            'email' => 'nullable|email|max:255',
            'paste_identifier' => 'nullable|string|max:10',
            'copyright_work' => 'nullable|string|max:1000',
            'authorization' => 'nullable|string|max:1000',
            'contact_info' => 'nullable|string|max:1000',
            'violation_type' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $request->all();
        // Respect no-logs policy: do not collect IP addresses or user agents
        $data['user_agent'] = null;
        
        // Map form fields to database fields
        $reportData = [
            'type' => $data['type'],
            'subject' => $data['subject'] ?? 'Support Request',
            'description' => $data['description'],
            'email' => $data['email'] ?? null,
            'paste_identifier' => $data['paste_identifier'] ?? null,
            'copyright_work' => $data['copyright_work'] ?? null,
            'authorization' => $data['authorization'] ?? null,
            'contact_info' => $data['contact_info'] ?? null,
            'violation_type' => $data['violation_type'] ?? null,
            'ip_address' => null, // No IP logging per privacy policy
            'user_agent' => null, // No user agent logging per privacy policy
        ];

        try {
            // Store the report in the database
            $report = SupportReport::create($reportData);
            
            // Send email notification
            $this->sendSupportEmail($data);
            
            return back()->with('success', 'Your report has been submitted successfully. We will respond within 24-48 hours.');
        } catch (\Exception $e) {
            return back()->with('error', 'There was an error submitting your report. Please try again or contact us directly.');
        }
    }

    private function sendSupportEmail($data)
    {
        $typeLabels = [
            'dmca' => 'DMCA Takedown Notice',
            'abuse' => 'Abuse Report',
            'general' => 'General Support',
            'security' => 'Security Issue',
            'appeal' => 'Policy Appeal'
        ];

        $subject = $typeLabels[$data['type']] . ': ' . $data['subject'];
        
        // In production, you'd send actual emails
        // You can implement actual email sending here:
        // Mail::to('support@dailyforever.com')->send(new SupportReportMail($data));
    }
}
