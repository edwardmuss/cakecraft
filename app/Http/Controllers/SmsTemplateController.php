<?php

namespace App\Http\Controllers;

use App\Models\SmsTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SmsTemplateController extends Controller
{
    // The master list of triggers your application supports
    protected $availableTriggers = [
        'order_created' => [
            'label' => 'Order Created',
            'description' => 'Sent immediately when a new invoice is created.',
            'placeholders' => '{CustomerName}, {Organization}, {InvoiceNumber}, {TotalAmount}, {Balance}, {OrderDetails}, {BranchName}, {InvoiceLink}, {PaymentLink}',
        ],
        'payment_received' => [
            'label' => 'Payment Received',
            'description' => 'Sent when a payment is recorded against an invoice.',
            'placeholders' => '{CustomerName}, {Organization}, {InvoiceNumber}, {PaymentAmount}, {TotalAmount}, {Balance}, {OrderDetails}, {BranchName}, {InvoiceLink}, {PaymentLink}, {ReceiptLink}',
        ],
        'delivered' => [
            'label' => 'Order Delivered',
            'description' => 'Sent when an order status is changed to Delivered.',
            'placeholders' => '{CustomerName}, {Organization}, {InvoiceNumber}, {TotalAmount}, {Balance}, {OrderDetails}, {BranchName}, {InvoiceLink}, {PaymentLink}',
        ],
        'aging_reminder_1_day' => [
            'label' => '1-Day Overdue Reminder',
            'description' => 'Sent automatically 1 day after the due date.',
            'placeholders' => '{CustomerName}, {Organization}, {InvoiceNumber}, {TotalAmount}, {Balance}, {OrderDetails}, {BranchName}, {InvoiceLink}, {PaymentLink}',
        ],
        'aging_reminder_7_days' => [
            'label' => '7-Day Overdue Reminder',
            'description' => 'Sent automatically 7 days after the due date.',
            'placeholders' => '{CustomerName}, {Organization}, {InvoiceNumber}, {TotalAmount}, {Balance}, {OrderDetails}, {BranchName}, {InvoiceLink}, {PaymentLink}',
        ],
    ];

    public function index()
    {
        $branchId = Auth::user()->branch_id;

        // Fetch existing templates for this branch
        $existingTemplates = SmsTemplate::where('branch_id', $branchId)
            ->get()
            ->keyBy('trigger_name');

        // Merge existing DB templates with our master list
        $templates = [];
        foreach ($this->availableTriggers as $trigger => $data) {
            $dbTemplate = $existingTemplates->get($trigger);
            $templates[$trigger] = [
                'label' => $data['label'],
                'description' => $data['description'],
                'placeholders' => $data['placeholders'],
                'message' => $dbTemplate ? $dbTemplate->message : '',
                'is_active' => $dbTemplate ? $dbTemplate->is_active : false,
            ];
        }

        return view('sms_templates.index', compact('templates'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'trigger_name' => 'required|string',
            'message' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $branchId = Auth::user()->branch_id;
        $isActive = $request->has('is_active') && $request->is_active == 'true' ? true : false;

        // updateOrCreate ensures a branch only ever has ONE template per trigger
        SmsTemplate::updateOrCreate(
            ['branch_id' => $branchId, 'trigger_name' => $request->trigger_name],
            ['message' => $request->message, 'is_active' => $isActive]
        );

        return response()->json(['success' => 'SMS Template updated successfully!']);
    }
}
