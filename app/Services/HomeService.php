<?php

namespace App\Services;

use App\Jobs\InquiryJobToAdmin;
use App\Jobs\InquiryJobToUser;
use App\Models\EmailTemplate;
use App\Models\Inquiry;
use Illuminate\Support\Facades\DB;
use Exception;

class HomeService
{
    public function storeInquiry(array $inquiryData)
    {
        try {
            DB::beginTransaction();
            $inquiryData['message'] = strip_tags($inquiryData['message']);
            $inquiryData['subject'] = strip_tags($inquiryData['subject']);
            $inquiry = Inquiry::create($inquiryData);

            DB::commit();
            
            $emailTemplateKeyUser = 'Contact us user';
            if ($emailTemplateKeyUser) {
                $emailTemplate = EmailTemplate::where('key', $emailTemplateKeyUser)->first();
                if ($emailTemplate && $emailTemplate->status) {
                    try {
                        InquiryJobToUser::dispatch($inquiry, $emailTemplate)->delay(now()->addSeconds(10));
                    } catch (Exception $e) {
                        //
                    }
                }
            }

            $emailTemplateKeyUser = 'Contact us admin';
            if ($emailTemplateKeyUser) {
                $emailTemplate = EmailTemplate::where('key', $emailTemplateKeyUser)->first();
                if ($emailTemplate && $emailTemplate->status) {
                    try {
                        InquiryJobToAdmin::dispatch($inquiry, $emailTemplate)->delay(now()->addSeconds(10));
                    } catch (Exception $e) {
                        //
                    }
                }
            }

            return true;

        }
        catch (Exception $e) {

            DB::rollBack();
            throw $e;
        }  

    } 
}
