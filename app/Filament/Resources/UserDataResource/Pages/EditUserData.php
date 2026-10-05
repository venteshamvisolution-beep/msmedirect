<?php

namespace App\Filament\Resources\UserDataResource\Pages;

use App\Filament\Resources\UserDataResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Mail\ApproveMail;
use App\Mail\DisApproveMail;
use Mail;
use App\Models\BusinessCategory;
use App\Models\SubBusinessCategory;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class EditUserData extends EditRecord
{
    protected static string $resource = UserDataResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['business_description'] = htmlentities($data['business_description']);

        return $data;
    }

    protected function beforeSave(): void
    {

        $record = $this->record;
    }

    protected function afterSave(): void
    {

        $record = $this->record;

        if ($record->business_category == "1") {
            $businessCategory = 'Others -' . $record->others;
        } else {
            $businessCategory = BusinessCategory::query()->where('id', $record->business_category)->value('name');
        }

        if ($record->business_type == 0) {
            $subbusinessCategory = SubBusinessCategory::query()->where('id', $record->business_sub_category)->value('name');
            $str = 'We are a <b>' . $record->type_organization . '</b> and a <b>' . $record->msme_type . '</b> MSME firm in <b>' . $businessCategory . ' ' . $subbusinessCategory . '</b> with a <b>' . $record->experience . '</b> years of experience and specialization in <b>' . $record->specialization . '</b> operating from <b>' . $record->city . ' ' . $record->state . '</b> and we also provide service to <b>' . $record->state_city . '</b>. I want to connect to <b>' . $record->want_to . '</b>.';
        } else {
            $str = 'We are a <b>' . $record->type_organization . '</b> and a <b>' . $record->msme_type . '</b> MSME firm in <b>' . $businessCategory . '</b> with a <b>' . $record->experience . '</b> years of experience and specialization in <b>' . $record->specialization . '</b> operating from <b>' . $record->city . ' ' . $record->state . '</b> and we also provide service to <b>' . $record->state_city . '</b>. I want to connect to <b>' . $record->want_to . '</b>.';
        }
        DB::table('user_data')->whereId($record->id)->update(['business_description' => $str]);



        if (isset($record->approve) && $record->approve == 1) {
            $userDetails = [];
            $countUser = DB::table('users')->where('email', $record->email)->count();

            if ($countUser < 1) {
                $password =  Str::random(10);
                $userDetails['email'] = $record->email;
                $userDetails['password'] = Hash::make($password);
                $userDetails['created_at'] = Carbon::now();
                $userDetails['name'] = $record->organization_name;
                if (!empty($record->msme_business_data_id)) {
                    $userDetails['msme_business_data_id'] = $record->msme_business_data_id;
                    $userDetails['is_bulk_user'] = 1;
                } else {
                    $userDetails['is_bulk_user'] = $record->is_bulk_user ?? 0;
                }

                $userId = DB::table('users')->insertGetId($userDetails);
                unset($userDetails['created_at']);
                $userDetails['password'] = $password;

                $notification = [];
                $notification['user_id'] = $userId;
                $notification['notification_id'] = 1; // Approval By Admin
                $notification['type'] = 'text';
                $notification['is_mail'] = true;
                $notification['is_mail_sent'] = true;
                $notification['created_at'] = Carbon::now();
                $businessCategory = BusinessCategory::query()->where('id', $record->business_category)->value('name');
                $notification['description'] = $businessCategory . ' Approved';
                $count = DB::table('front_notifications')->where($notification)->count();
                if ($count == 0) {
                    DB::table('front_notifications')->insert($notification);
                }

                Mail::to($record->email)->send(new ApproveMail($userDetails));
            }
        } else {
            $userDetails = [];
            $userDetails['reason'] = $record->remark;

            if (isset($record->remark) && $record->remark != '') {
                $userDetails['reason'] = $record->remark;
                Mail::to($record->email)->send(new DisApproveMail($userDetails));
            }
        }
    }
}
