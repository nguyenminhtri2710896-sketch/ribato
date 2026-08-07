<?php

namespace App\Http\Controllers\Frontend\V1;


class IndexController extends BaseController
{


        /**
         * Create a new controller instance.
         *
         * @return void
         */
        public function __construct()
        {


        }

        public function index()
        {
                return view("frontend.v1.index.index")->with([]);

        }


    public function contact(\Illuminate\Http\Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'company' => 'nullable|string|max:255',
            'message' => 'required|string',
        ]);

        $entry = sprintf(
            "[%s] Name: %s | Email: %s | Company: %s | Message: %s\n",
            now()->toDateTimeString(),
            $validated['full_name'],
            $validated['email'],
            $validated['company'] ?? 'N/A',
            $validated['message']
        );

        // Append to a file in storage/app
        $filePath = storage_path('app/contact_submissions.txt');
        file_put_contents($filePath, $entry, FILE_APPEND);

        return response()->json(['success' => true, 'message' => 'Thank you for contacting us!']);
    }
}