<?php

namespace App\Filament\Resources\NewUserDataResource\Pages;

use App\Filament\Resources\NewUserDataResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Mail\ApproveMail;
use App\Mail\DisApproveMail;
use Mail;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class EditNewUserData extends EditRecord
{
    protected static string $resource = NewUserDataResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    
    protected function afterSave(): void
    {
        
        $record = $this->record;
        if(isset($record->approve) && $record->approve == 1){
            $userDetails = [];
            
            $recordData = DB::table('new_user_data')->select('organization_name',
        'business_type',
        'type_organization',
        'msme_type',
        'business_category',
        'business_sub_category',
        'specialization',
        'contact_no',
        'email',
        'pincode',
        'city',
        'state',
        'experience',
        'document_no',
        'document_file',
        'state_city',
        'business_size',
        'approve',
        'address',
        'want_to',
        'remark')->where('id',$record->id)->get()->toArray();
            
            $recordData = (array) current($recordData);
            $recordData['updated_at'] = Carbon::now();
            $recordData['status'] = "approved";
            DB::table('user_data')->where('email',$record->email)->update($recordData);
            
            // DB::table('new_user_data')->where('email',$record->email)->delete();
            
            
            
            
        }else{
            $userDetails = [];
            $userDetails['reason'] = $record->remark;
            // DB::table('user_data')->where('email',Auth::user()->email)->update(['status' => "rejected"]);
            
            
        
            
            
            
        }
    }
}
